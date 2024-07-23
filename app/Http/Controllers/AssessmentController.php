<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
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

    public function continueEvaluate($id,$token,$lang)
    {
        $assesment = $this->getDataAssessment($id,$token,$lang);
        return view('dashboard.users.evaluate',compact('assesment'));
    }

    public function newEvaluation($respondentId, $locale)
    {
        $email = User::where('account_id',$respondentId)->first();
        $this->createNewEvaluation($respondentId,$locale,array($email));

        return redirect()->back()->with('success','Se ha creado correctamente la evaluación.');
    }

    public function sendEmailEvaluate(Request $request)
    {
        $data = $this->sendEmailEvaluation($request->id);
        return response()->json([
            'success' => 'Se ha enviado correctamente el correo para hacer la evaluación, revisa tu bandeja de entrada / SPAM.',
            'data' => $data,
        ], 200); 
    }

    public function startEvaluate($id,$token,$lang)
    {
        $this->startEvaluation($id,$token,$lang);
    }

    public function createNewUser(Request $request)
    {
        //Creamos el usuario en la plataforma
        $account_id = $this->createUser($request->name,$request->lastname,$request->email,$request->gender,$request->locale);

        if($account_id){
            //Creamos el usuario en la base de datos
            $user = new User();
            $user->name = $request->name;
            $user->last_name = $request->lastname;
            $user->email = $request->email;
            $user->lang = $request->locale;
            $user->password = bcrypt($request->password);
            $user->account_id = $account_id;
            $user->user_id = Auth::user()->id;
            $user->assignRole($request->role);
            $user->save();

            return redirect()->back()->with('success','Se ha creado el usuario correctamente.');
        }
         else {
            return redirect()->back()->with('error','Ha ocurrido un error al dar de alta al usuario.');
         }
        
    }
}
