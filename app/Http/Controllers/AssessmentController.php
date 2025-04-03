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
        $locale = $user->lang ?? 'en-US';
        $items = $this->getReportAssessment($id,$locale);
        $reports = $this->getReportAssessmentPDF($id,$locale);
        $content = '';

        if (!$assessment) {
            $assessment = new Assessment();
            $assessment->assessment_id = $id;
            $assessment->save();
        }

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

        if (isset($assessment)) {
            $assessment->save();
        }

        $pdf_interest = url(Storage::url($assessment->interest_url));
        $pdf_individual = url(Storage::url($assessment->individual_url));
        
        if (empty($assessment->openia)) {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdf_interest);
            
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
        
        $prompt = "Si esta ingles traducelo y haz lo siguiente, basado en el siguiente texto que contiene intereses y habilidades profesionales, 
                  sugiere 5 trabajos actualizados y relevantes en el mercado laboral actual, enfocate en la actualidad nada de vacantes viejas / antiguas. 
                  Para cada trabajo, explica brevemente por qué sería una buena opción y dame un texto de introduccion principal antes de los 5 trabajos y en la parte de los lista desordenada de los 5 trabajos ponle un titulo que diga 5 trabajos recomendados:, cierralo con un mensaje motivador, formatealo en html, no pongas fechas, solo deja el <body> no necesito cabecera ni nada, el titulo que sea h2, usa p para parrafos, li para lista ordenada:\n\n" . $pdfText;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);
        
        return $response->choices[0]->message->content;
    }

}
