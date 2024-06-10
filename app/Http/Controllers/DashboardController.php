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
                $user->password = bcrypt('password');
                $user->assignRole('respondent');
                $user->save();
            } else {
                echo 'Ya se encuentran los usuarios';
            }
            
        }

        return redirect()->back()->with('success','Se ha sincronizado los usuarios.');
    }

    public function welcome(UsersDataTable $dataTable){
        $respondents = $this->getRespondents();
        return $dataTable->render('dashboard.index',compact('respondents'));
    }

    public function superLink($emails,$idTemplate){
        return $this->sendSuperLink($emails,$idTemplate);
    }
}
