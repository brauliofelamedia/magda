<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Imports\UsersImport;
use App\Models\Category;
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

        if(Auth::user()->hasRole('respondent')){
            return view('dashboard.assessments.welcome');
        }

        $users = User::query()
            ->when(request('search'), function($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                      ->orWhere('email', 'like', '%' . request('search') . '%');
            })
            ->when(request('category'), function($query) {
                $query->where('category_id', request('category'));
            });

        if(Auth::user()->hasRole('administrator')){
            $users = $users;
        } else if(Auth::user()->hasRole('institution')){
            $users = $users->where('user_id', Auth::user()->id)->role(['respondent']);
        }

        $users = $users->paginate(10)->onEachSide(1);
        
        $locales = config('languages.locales');
        $institutions = User::whereHas('roles', function ($query) {
            $query->where('name', 'institution');
        })->get();

        $categories = Category::where('user_id',Auth::user()->id)->get();

        return view('dashboard.index',compact('users','locales','institutions','categories'));
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
