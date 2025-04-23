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
        return view('assessments.welcome');
    }

    public function newEvaluation($respondentId, $locale = 'es-ES')
    {   
        $email = User::where('account_id',$respondentId)->first();
        $id_return = $this->createNewEvaluation($respondentId,$locale,array($email));
        $assesment = $this->getAssesment($id_return);
        return $this->startEvaluate($respondentId,$assesment['id'],$assesment['token'],$locale);
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
        $items = $this->getReportAssessment($id,$locale);
        $reports = $this->getReportAssessmentPDF($id,$locale);
        $content = '';

        if (!$assessment) {
            $assessment = new Assessment();
            $assessment->assessment_id = $id;
            $assessment->save();
        }

        if (!empty($reports['interests'])) {
            if ($assessment->interest_url == null) {
                $interestsContent = file_get_contents($reports['interests']);
                $interestsPath = 'assessments/' . $id . '_interests.pdf';
                Storage::disk('public')->put($interestsPath, $interestsContent);
                $assessment->interest_url = $interestsPath;
            }
        }

        if (!empty($reports['individual'])) {
            if ($assessment->individual_url == null) {
                $individualContent = file_get_contents($reports['individual']);
                $individualPath = 'assessments/' . $id . '_individual.pdf';
                Storage::disk('public')->put($individualPath, $individualContent);
                $assessment->individual_url = $individualPath;
            }
        }

        if (isset($assessment)) {
            $assessment->save();
        }

        $pdf_interest = null;
        $pdf_individual = null;

        if ($assessment->interest_url && Storage::disk('public')->exists($assessment->interest_url)) {
            $pdf_interest = url(Storage::url($assessment->interest_url));
            $pdfPath = storage_path('app/public/' . $assessment->interest_url);
        } elseif ($assessment->individual_url && Storage::disk('public')->exists($assessment->individual_url)) {
            $pdf_individual = url(Storage::url($assessment->individual_url));
            $pdfPath = storage_path('app/public/' . $assessment->individual_url);
        }
        
        if (empty($assessment->openia) && isset($pdfPath)) {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            
            // Get all pages and limit to first 5
            $pages = $pdf->getPages();
            $pageLimit = min(5, count($pages));
            
            // Extract text from first 5 pages only
            for ($i = 0; $i < $pageLimit; $i++) {
            $content .= $pages[$i]->getText();
            }

            $returnOpenAI = $this->analyzePDFWithOpenAI($content);
            $assessment->openia = $returnOpenAI;
            $assessment->save();
        }

        return view('dashboard.users.finish',compact('items','reports','user','assessment','pdf_interest','pdf_individual'));
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
        if($data['data']['createRespondent']){
            //Creamos el usuario en la base de datos
            $user = new User();
            $user->name = $request->name;
            $user->last_name = $request->lastname;
            $user->email = $request->email;
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

            return redirect()->back()->with('error',$data['errors'][0]['message']);
         }

    }

    private function analyzePDFWithOpenAI($pdfText) {
        $apiKey = env('OPENAI_API_KEY');
        
        $prompt = "Traducelo al español y haz lo siguiente, Actúa como un orientador vocacional con experiencia en desarrollo de carrera y análisis de perfiles. A continuación, recibirás un informe completo de intereses ocupacionales generado a través del assessment 'Tu Talento Finder' para un individuo. Tu tarea es leer y analizar dicho informe con atención.
Basándote en:
1. Los tres intereses ocupacionales más altos del participante (en orden de prioridad).
2. Las descripciones detalladas de esos tipos de interés.
3. Las ocupaciones sugeridas en las categorías profesionales del informe.
4. La compatibilidad porcentual si está incluida.
5. Los pasatiempos y motivadores asociados a los intereses dominantes.
Genera lo siguiente:
1. Las **5 profesiones ideales** para el participante, al día de hoy, que estén alineadas con sus intereses, motivadores y nivel de preparación actual (puedes hacer suposiciones razonables si no se incluye nivel de estudios).
2. Las **5 mejores ideas de emprendimiento** que podrían entusiasmar y retar al participante, considerando sus motivadores personales, como el liderazgo, la autonomía, la creatividad o la interacción con personas.
3. Justifica brevemente cada recomendación (1-2 líneas por cada profesión o emprendimiento).
IMPORTANTE: Las recomendaciones deben ser prácticas, relevantes al contexto actual del mercado laboral, y ofrecer tanto opciones tradicionales como innovadoras. Sé concreto, creativo y profesional.
Este es el informe para analizar: generalo en html solo entregame el body, no pongas fechas, sin header ordenando la lista, parrafos y titulos principal (h1), titulos secundarios (h2) y otros titulo (h3). \n\n" . $pdfText;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);
        
        return $response->choices[0]->message->content;
    }

}
