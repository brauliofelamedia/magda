<?php

namespace App\Http\Controllers;

use App\Mail\AssignEvaluate;
use App\Mail\SendCreateUser;
use App\Models\Assessment;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\APICalls;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    use APICalls;

    public function getAssessments($respondentId)
    {
        $user = User::where('account_id',$respondentId)->first();
        $assesments = $this->getAssessmentUser($respondentId);
        return view('dashboard.assessments.index',compact('assesments','user'));
    }

public function welcome()
    {
        $institutions = User::role('institution')->get();
        return view('assessments.welcome', compact('institutions'));
    }

    public function newEvaluation(Request $request)
    {   
        $user = User::where('account_id',$request->respondentId)->first();
        $type = $request->type;
        
        $lang = $user->lang ?? 'en-US';
        if ($lang === 'es-MX') {
            $lang = 'es-ES';
        }

        $id_return = $this->createNewEvaluation($request->respondentId,$lang,array($user->email),$type);
        $assesment = $this->getAssesment($id_return);
        return $this->startEvaluate($request->respondentId,$assesment['id'],$assesment['token'],$lang);
    }

    public function startEvaluate($respondentId,$id,$token,$lang)
    {
        $id_return = $this->startEvaluation($id,$token,$lang);

        //Get user
        $user = User::where('account_id',$respondentId)->first();
        $url = route('assessments.continue',[$respondentId,$id_return,$token,$lang]);

        if(Auth::user()->hasRole('institution')){
            Mail::to($user->email)->send(new AssignEvaluate($user,$url));
            return redirect()->route('assessments.index',$respondentId);
        } else {
            return redirect()->route('assessments.continue',[$respondentId,$id_return,$token,$lang]);
        }
    }

    public function updateAnswersAssessment(Request $request)
    {
        $data = $this->updateAnswers($request->id,$request->token,$request->responses);
        return response()->json([
            'success' => 'Se recibio correctamente la respuesta.',
            'data' => $data,
        ], 200);
    }

    public function finish($id)
    {
        $user = Auth()->user();
        $assessment = Assessment::where('assessment_id', $id)->first();
        $locale = 'es-PR';
        
        // Usar caché para evitar llamadas API repetidas (24 horas de caché)
        $cacheKey = 'report_assessment_' . $id;
        $items = cache()->remember($cacheKey, 86400, function() use($id, $locale) {
            return $this->getReportAssessment($id, $locale);
        });
        
        if (!$assessment) {
            $assessment = new Assessment();
            $assessment->assessment_id = $id;
            $assessment->save();
            
            // Si es nuevo, procesamos los reportes PDF en segundo plano
            $this->processPDFs($id, $locale, $assessment);
        }

        $pdf_interest = null;
        $pdf_individual = null;

        if ($assessment->interest_url && Storage::disk('public')->exists($assessment->interest_url)) {
            $pdf_interest = url(Storage::url($assessment->interest_url));
        }
        
        if ($assessment->individual_url && Storage::disk('public')->exists($assessment->individual_url)) {
            $pdf_individual = url(Storage::url($assessment->individual_url));
        }
        
        // Procesar con OpenAI en segundo plano si no está hecho
        if (empty($assessment->openia) || empty($assessment->resumen_openia)) {
            if ($assessment->is_processing != 1) {
                $assessment->is_processing = 1;
                $assessment->save();
                
                // En un entorno real, esto se manejaría con colas
                // Por ahora hacemos una versión simplificada
                $this->processOpenAI($assessment);
            }
        }
        
        // Devolver la vista aunque no esté completamente procesado
        // El usuario verá los resultados incluso si OpenAI no ha terminado
        return view('dashboard.users.finish',compact('items','user','assessment','pdf_interest','pdf_individual'));
    }
    
    /**
     * Procesa los PDFs en segundo plano
     */
    private function processPDFs($id, $locale, $assessment)
    {
        // En un sistema de producción, esto iría a una cola
        $reports = $this->getReportAssessmentPDF($id, $locale);
        
        if (!empty($reports['interests']) && $assessment->interest_url == null) {
            $interestsContent = file_get_contents($reports['interests']);
            $interestsPath = 'assessments/' . $id . '_interests.pdf';
            Storage::disk('public')->put($interestsPath, $interestsContent);
            $assessment->interest_url = $interestsPath;
        }

        if (!empty($reports['individual']) && $assessment->individual_url == null) {
            $individualContent = file_get_contents($reports['individual']);
            $individualPath = 'assessments/' . $id . '_individual.pdf';
            Storage::disk('public')->put($individualPath, $individualContent);
            $assessment->individual_url = $individualPath;
        }

        $assessment->save();
    }
    
    /**
     * Procesa el análisis de OpenAI
     */
    private function processOpenAI($assessment)
    {
        try {
            // Solo procesar si tenemos un PDF disponible
            if (($assessment->interest_url && Storage::disk('public')->exists($assessment->interest_url)) || 
                ($assessment->individual_url && Storage::disk('public')->exists($assessment->individual_url))) {
                
                $pdfPath = null;
                if ($assessment->interest_url) {
                    $pdfPath = storage_path('app/public/' . $assessment->interest_url);
                } elseif ($assessment->individual_url) {
                    $pdfPath = storage_path('app/public/' . $assessment->individual_url);
                }
                
                if ($pdfPath) {
                    // Procesamos el PDF y hacemos una sola llamada a OpenAI
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($pdfPath);
                    
                    $content = '';
                    $pages = $pdf->getPages();
                    $pageLimit = min(5, count($pages));
                    
                    for ($i = 0; $i < $pageLimit; $i++) {
                        $content .= $pages[$i]->getText();
                    }
                    
                    // Usamos un prompt combinado para generar ambos resultados en una sola llamada
                    $returnOpenAI = $this->combinedOpenAIAnalysis($content);
                    
                    // Si tenemos una respuesta, la guardamos
                    if (!empty($returnOpenAI) && isset($returnOpenAI['main'], $returnOpenAI['summary'])) {
                        $assessment->openia = $returnOpenAI['main'];
                        $assessment->resumen_openia = $returnOpenAI['summary'];
                        $assessment->is_processing = 0;
                        $assessment->save();
                    }
                }
            }
        } catch (\Exception $e) {
            // Log del error pero permitir que la página se siga mostrando
            \Log::error('Error procesando OpenAI: ' . $e->getMessage());
            $assessment->is_processing = 0;
            $assessment->save();
        }
    }

    public function closeAssessment(Request $request)
    {
        $close = $this->closeAnswers($request->id,$request->token);

        //Generar una notificación
        $notify = new Notification();
        $notify->user_id = Auth::user()->id;
        $notify->info = '';
        $notify->save();

        return response()->json([
            'success' => 'Se cerro correctamente la evaluación.',
            'data'=> $close
        ], 200);
    }

    public function continueEvaluate($userId,$id,$token,$lang)
    {
        $assesments = $this->getDataAssessment($id,$token,$lang);
        $assesments = collect($assesments)->sortBy(function($section, $key) {
            return $key === 'cognitive' ? 0 : 1;
        })->toArray();
        //dd($assesments);
        return view('dashboard.users.evaluate',compact('assesments'));
    }

    public function sendEmailEvaluate(Request $request)
    {
        $data = $this->sendEmailEvaluation($request->id);
        return response()->json([
            'success' => 'Se ha enviado correctamente el correo para hacer la evaluación, revisa tu bandeja de entrada / SPAM.',
            'data' => $data,
        ], 200);
    }

    public function createNewUser(Request $request)
    {
        //Creamos el usuario en la plataforma
        $data = $this->createUser($request->name,$request->lastname,$request->email,$request->gender,$request->locale);
        
        // Verificar si ocurrió un error de excepción (string) o si la respuesta no tiene la estructura esperada
        if (is_string($data)) {
            return redirect()->back()->with('error', 'Error al conectar con la API: ' . $data);
        }

        if(isset($data['data']['createRespondent']) && $data['data']['createRespondent']){
            //Creamos el usuario en la base de datos
            $user = new User();
            $user->name = $request->name;
            $user->last_name = $request->lastname;
            $user->email = $request->email;
            $user->type_of_evaluation = $request->type_of_evaluation;
            $user->lang = $request->locale;
            $user->user_id = $request->user_id;
            $user->account_id = $data['data']['createRespondent']['respondent']['id'];
            $user->password = bcrypt($request->password);
            $user->assignRole($request->role);
            $user->save();

            //Generate password / Save passwords
            if($request->password){
                $user->password = bcrypt($request->password);
                $user->save();
                Mail::to($user->email)->send(new SendCreateUser($user,$request->password));
            } else {
                $passwordRandom = Str::random(10);
                $user->password = bcrypt($passwordRandom);
                $user->save();

                Mail::to($user->email)->send(new SendCreateUser($user,$passwordRandom));
            }

            if($request->name_institution){
                $user->name_institution = $request->name_institution;
                $user->save();
            }

            return redirect()->back()->with('success','Se ha creado el usuario correctamente.');
        } else {
            $errorMessage = isset($data['errors'][0]['message']) ? $data['errors'][0]['message'] : 'Error desconocido al crear el usuario.';
            return redirect()->back()->with('error', $errorMessage);
         }

    }

    private function combinedOpenAIAnalysis($pdfText) {
        try {
            $prompt = "Traducelo al español y haz lo siguiente, Actúa como un orientador vocacional con experiencia en desarrollo de carrera y análisis de perfiles. El estudiante acaba de completar la evaluación Tu Talento Finder. A continuación, recibirás un informe completo de intereses ocupacionales generado a través del assessment 'Tu Talento Finder' para un individuo. Tu tarea es leer y analizar dicho informe con atención y entregar una respuesta clara, motivadora y sencilla (nivel de lectura 8vo grado).

Basándote en:
1. Los tres intereses ocupacionales más altos del participante (en orden de prioridad).
2. Las descripciones detalladas de esos tipos de interés.
3. Las ocupaciones sugeridas en las categorías profesionales del informe.
4. La compatibilidad porcentual si está incluida.
5. Los pasatiempos y motivadores asociados a los intereses dominantes.

Necesito que generes dos secciones en formato JSON:
1. Una sección 'main' con análisis completo detallado, incluyendo:
   - Las 5 carreras profesionales o vocacionales que más se alinean con el perfil del estudiante y los programas académicos en Puerto Rico necesarios para esas 5 carreras
   - 5 ideas de emprendimiento viables en Puerto Rico que conecten con sus talentos y personalidad, con una inversión máxima de \$1,000
   - Una reflexión motivadora con tono juvenil, que invite a creer en su potencial y tomar acción
   - Justificaciones para cada recomendación
   - Formato en HTML (solo el body, sin fechas, sin header)
   - Usa viñetas para que sea fácil de leer
   - Tono amigable, claro, inspirador y breve
   - Evita lenguaje técnico

2. Una sección 'summary' con un resumen conciso, incluyendo:
   - Listado de las 5 carreras profesionales recomendadas con sus programas académicos en Puerto Rico
   - Listado de las 5 ideas de emprendimiento (con inversión máxima de \$1,000)
   - Breve justificación para cada una (1-2 líneas)
   - Formato en HTML (solo el body, sin fechas, sin header)
   - Tono amigable y claro (nivel 8vo grado)
   - Usa viñetas
   - No hagas referencias a ChatGPT ni a IA en el texto.

El formato de respuesta debe ser JSON exactamente así:
{
  \"main\": \"<aquí va el HTML del análisis detallado>\",
  \"summary\": \"<aquí va el HTML del resumen>\"
}

IMPORTANTE: 
- Las recomendaciones deben ser prácticas, relevantes al contexto actual del mercado laboral en Puerto Rico, y ofrecer tanto opciones tradicionales como innovadoras.
- Sé concreto, creativo y profesional, pero mantén un tono juvenil y motivador.
- La respuesta debe ser escrita en tono amigable, claro, inspirador y breve (nivel de lectura 8vo grado).
- Evita lenguaje técnico y usa viñetas para facilitar la lectura.

Este es el informe para analizar: \n\n" . $pdfText;

            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo-16k', // Usar modelo con contexto más grande para mejor manejo de documentos
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object'], // Asegurarnos que devuelva JSON
            ]);
            
            // Decodificar el JSON de respuesta
            $content = $response->choices[0]->message->content;
            $result = json_decode($content, true);
            
            // Si la respuesta no es un JSON válido o no tiene los campos esperados, devolvemos un formato predeterminado
            if (!is_array($result) || !isset($result['main']) || !isset($result['summary'])) {
                return [
                    'main' => $content, // Si falló, usar todo el contenido como main
                    'summary' => '<h1>Resumen no disponible</h1><p>No se pudo generar un resumen adecuado.</p>'
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('Error en OpenAI: ' . $e->getMessage());
            return [
                'main' => '<h1>Análisis no disponible</h1><p>Hubo un error al procesar el informe. Por favor inténtalo más tarde.</p>',
                'summary' => '<h1>Resumen no disponible</h1><p>Hubo un error al procesar el informe. Por favor inténtalo más tarde.</p>'
            ];
        }
    }
    
    // Mantenemos los métodos originales para compatibilidad con código existente
    private function analyzePDFWithOpenAI($pdfText) {
        $result = $this->combinedOpenAIAnalysis($pdfText);
        return $result['main'];
    }
    
    private function generateResumenOpenAI($openiaContent) {
        // Si ya tenemos el contenido principal, intentamos extraer un resumen del mismo
        $prompt = "Crea un resumen conciso del siguiente análisis vocacional. Incluye las 5 profesiones recomendadas y las 5 ideas de emprendimiento con una breve justificación para cada una (1-2 líneas). Formatea la respuesta en HTML limpio sin fechas ni headers: \n\n" . $openiaContent;

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
            ]);
            
            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            \Log::error('Error generando resumen: ' . $e->getMessage());
            return '<h1>Resumen no disponible</h1><p>No se pudo generar un resumen del análisis.</p>';
        }
    }

}
