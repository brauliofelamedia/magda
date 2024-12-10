<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\APICalls;
use Illuminate\Support\Facades\Mail;
use App\Mail\Welcome;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use APICalls;
    
    public function edit($uuid)
    {
        $user = User::where('uuid',$uuid)->first();
        $institutes = User::role('institution')->get();
        return view('dashboard.users.edit',compact('user','institutes'));
    }

    public function update(Request $request,$uuid)
    {
        $user = User::where('uuid',$uuid)->first();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_id = $request->user_id;
        $user->assignRole($request->role);

        if($request->name_institution){
            $user->name_institution = $request->name_institution;
            $user->save();
        }


        if($request->avatar){
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        
            if ($request->hasFile('avatar')) {
                $imagePath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $imagePath;
            }
        }

        if($request->password == $request->password_confirmation){
            $user->password = Hash::make($request->input('password'));
        }
        
        $user->save();
        return redirect()->route('users.edit',$uuid)->with('success', 'Se han actualizado los datos correctamente.');
    }

    public function sendEmailWelcome(Request $request)
    {
        $user = User::find($request->id);

        Mail::to($user->email)->send(new Welcome($user));
        
        return response()->json([
            'success' => 'Se ha enviado el correo',
            'data' => $user
        ], 200); 
    }
}
