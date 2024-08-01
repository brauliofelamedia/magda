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

    public function welcome(){
        if (auth()->user()->hasRole('administrator')) {
            $users = User::get();
        } elseif(auth()->user()->hasRole('institution')){
            $users = User::where('user_id',auth()->user()->id)->role(['respondent'])->get();
        } elseif(auth()->user()->hasRole('respondent')){
            return redirect()->route('assessments.index',auth()->user()->account_id);
        }

        $locales = [
            'fr-FR' => 'Francés (Francia)',
            'de-DE' => 'Alemán (Alemania)',
            'en-US' => 'Inglés (Estados Unidos)',
            'pt-BR' => 'Portugués (Brasil)',
            'en-GB' => 'Inglés (Reino Unido)',
            'es-ES' => 'Español (España)',
            'ar' => 'Árabe',
            'el' => 'Griego',
            'pt-AO' => 'Portugués (Angola)',
            'da' => 'Danés',
            'ja' => 'Japonés',
            'hu' => 'Húngaro',
            'vi' => 'Vietnamita',
            'en-CA' => 'Inglés (Canadá)',
            'es-MX' => 'Español (México)',
            'sv' => 'Sueco',
            'lt' => 'Lituano',
            'tr' => 'Turco',
            'fr-CA' => 'Francés (Canadá)',
            'fi' => 'Finlandés',
            'nl' => 'Neerlandés',
            'en-AU' => 'Inglés (Australia)',
            'pl' => 'Polaco',
            'fo' => 'Feroés',
            'lv' => 'Letón',
            'nb' => 'Noruego Bokmål',
            'ru' => 'Ruso',
            'es-PE' => 'Español (Perú)',
            'es-PR' => 'Español (Puerto Rico)',
            'it' => 'Italiano',
            'pt-PT' => 'Portugués (Portugal)',
            'ro' => 'Rumano',
            'is' => 'Islandés',
            'mk' => 'Macedonio',
            'sr' => 'Serbio',
            'sk' => 'Eslovaco',
            'si' => 'Esloveno',
            'es-DO' => 'Español (República Dominicana)',
            'bg' => 'Búlgaro',
            'en-IE' => 'Inglés (Irlanda)'
        ];
        
        return view('dashboard.index',compact('locales','users'));
    }

    public function superLink($emails,$idTemplate){
        return $this->sendSuperLink($emails,$idTemplate);
    }
}
