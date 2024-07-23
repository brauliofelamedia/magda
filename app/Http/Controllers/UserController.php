<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\APICalls;
use Illuminate\Support\Facades\Mail;
use App\Mail\Welcome;

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
        $user = User::where('uuid',$uuid);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $uuid,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_id = $request->user_id;
        $user->assignRole($request->role);

        if($request->password == $request->password_confirmation){
            $user->password = Hash::make($request->input('password'));
        }
        
        $user->save();
        return redirect()->route('dashboard.welcome')->with('success', 'Se han actualizado los datos correctamente.');
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
