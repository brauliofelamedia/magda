<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\APICalls;

class UserController extends Controller
{
    use APICalls;
    public function getAssessment($respondentId)
    {
        $user = User::where('account_id',$respondentId)->first();
        $assesments = $this->getAssessmentUser($respondentId);
        return view('dashboard.users.assessments',compact('assesments','user'));
    }
    
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.edit',compact('user'));
    }

    public function getReportAssessment(Request $request)
    {
        $data = $this->getReportAssessment($request->respondentId);
        
        return response()->json([
            'mensaje' => '¡Éxito!',
            'datos' => $data
        ], 200); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if($request->password == $request->password_confirmation){
            $user->password = Hash::make($request->input('password'));
        }
        
        $user->save();
        return redirect()->back()->with('success', 'Se han actualizado los datos correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
