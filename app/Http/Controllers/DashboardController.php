<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\APICalls;
use App\Models\Notification;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

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
                $user->lang = $respondent['node']['locale'] ?? 'es-ES';
                $user->platform = true;
                $user->password = bcrypt('password');
                $user->assignRole('respondent');
                $user->user_id = 4;
                $user->save();
            }

        }

        return redirect()->back()->with('success','Se ha sincronizado los usuarios.');
    }

    public function welcome(){
        //dd(Auth::user()->roles);
        $locales = config('languages.locales');
        $institutions = User::whereHas('roles', function ($query) {
            $query->where('name', 'institution');
        })->get();

        if(Auth::user()->hasRole('administrator')){
            $users = User::all();
        } else if(Auth::user()->hasRole('institution')){
            $users = User::where('user_id',Auth::user()->id)->role(['respondent'])->get();

        } else if(Auth::user()->hasRole('respondent')){
            return view('dashboard.assessments.welcome');
        }

        return view('dashboard.index',compact('users','locales','institutions'));
    }

    public function import()
    {
        return view('dashboard.import');
    }

    public function import_process(Request $request)
    {
        Excel::import(new UsersImport, $request->file('file'));
        return redirect()->back()->with('success', 'Se ha importado correctamente los usuarios.');
    }

    public function remove_notification(Request $request)
    {
        if($request->remove == true){
            $users = User::where('user_id',Auth::user()->id)->get();
            $userIds = $users->pluck('id')->toArray();
            $notification = Notification::whereIn('user_id', $userIds)
                            ->where('status', false)
                            ->update(['status' => true]);
            return redirect()->back()->with('success', 'Se han eliminado las notificaciones');
        }
    }

    public function superLink($emails,$idTemplate){
        return $this->sendSuperLink($emails,$idTemplate);
    }
}
