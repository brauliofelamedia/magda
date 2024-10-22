<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\APICalls;
use App\Models\User;

class DashboardController extends Controller
{
    use APICalls;

    public function index()
    {
        return redirect()->route('login');
    }

    public function syncUsers()
    {
        //Obtenemos los usuarios del sistema y de la aplicación
        $respondents = $this->getRespondents();
        $users = User::get();

        foreach($respondents as $respondent){

            $checkUser = User::where('email',$respondent['node']['email'])->first();

            if(!$checkUser){
                $user = new User();
                $user->name = $respondent['node']['firstName'].' '.$respondent['node']['lastName'];
                $user->email = $respondent['node']['email'];
                $user->account_id = $respondent['node']['id'];
                $user->lang = $respondent['node']['locale'];
                $user->platform = true;
                $user->password = bcrypt('password');
                $user->assignRole('respondent');
                $user->user_id = 4;
                $user->save();
            }
            
        }

        return redirect()->back()->with('success','Se ha sincronizado los usuarios.');
    }

    public function welcome(UsersDataTable $dataTable){
        $locales = config('languages.locales');
        
        if(auth()->user()->hasRole('respondent')){
            return redirect()->route('assessments.index',auth()->user()->account_id);
        } else {
            return $dataTable->render('dashboard.index', compact('locales'));
        }
    }

    public function superLink($emails,$idTemplate){
        return $this->sendSuperLink($emails,$idTemplate);
    }
}
