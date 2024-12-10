<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\APICalls;
use Illuminate\Support\Facades\Auth;

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

    public function newEvaluation($respondentId, $locale)
    {
        $email = User::where('account_id',$respondentId)->first();
        $id_return = $this->createNewEvaluation($respondentId,$locale,array($email));
        $assesment = $this->getAssesment($id_return);
        return $this->startEvaluate($respondentId,$assesment['id'],$assesment['token'],$locale);
    }

    public function startEvaluate($respondentId,$id,$token,$lang)
    {
        $id_return = $this->startEvaluation($id,$token,$lang);
        return redirect()->route('assessments.continue',[$respondentId,$id_return,$token,$lang]);
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
        $locale = 'es-PR';
        $items = $this->getReportAssessment($id,$locale);
        $reportPDF = $this->getReportAssessmentPDF($id,$locale);
        $user = Auth()->user();
        return view('dashboard.users.finish',compact('items','reportPDF','user'));
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
        //dd($request->all());
        $account_id = $this->createUser($request->name,$request->lastname,$request->email,$request->gender,$request->locale);
        dd($account_id);

        if($account_id){
            //Creamos el usuario en la base de datos
            $user = new User();
            $user->name = $request->name;
            $user->last_name = $request->lastname;
            $user->email = $request->email;
            $user->lang = $request->locale;
            $user->password = bcrypt($request->password);
            $user->account_id = $account_id;
            $user->assignRole($request->role);
            $user->save();

            if($request->name_institution && $request->role == 'institution'){
                $user->user_id = Auth::user()->id;
                $user->save();
            }

            if($request->name_institution){
                $user->name_institution = $request->name_institution;
                $user->save();
            }

            return redirect()->back()->with('success','Se ha creado el usuario correctamente.');
        } else {
            return redirect()->back()->with('error','Ha ocurrido un error al dar de alta al usuario');
         }
        
    }
}
