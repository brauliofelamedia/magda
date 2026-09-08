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
use Illuminate\Support\Str;
use App\Mail\resetPassword;

class UserController extends Controller
{
    use APICalls;

    public function edit($uuid)
    {
        $user = User::where('uuid', $uuid)->first();
        $institutes = User::role('institution')->get();
        return view('dashboard.users.edit', compact('user', 'institutes'));
    }

    public function update(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8|confirmed',
            'type_of_evaluation' => 'nullable',
        ]);

        $data['last_name'] = $request->last_name;
        $data['user_id'] = $request->user_id;

        if ($request->has('type_of_evaluation')) {
            $data['type_of_evaluation'] = $request->type_of_evaluation;
        }

        if ($request->has('role')) {
            $user->syncRoles($request->role);
        }

        if ($request->name_institution) {
            $data['name_institution'] = $request->name_institution;
        }

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $file = $request->file('avatar');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $destDir = storage_path('app/public/avatars');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0775, true);
            }
            $file->move($destDir, $filename);
            $data['avatar'] = 'avatars/' . $filename;
        }

        if ($request->password == $request->password_confirmation) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        // Si es institución y cambió sus tipos de evaluación, propagarlos a los evaluados asociados que no tengan asignación propia
        if ($user->hasRole('institution') && $request->has('type_of_evaluation')) {
            User::where('user_id', $user->id)
                ->role('respondent')
                ->whereNull('type_of_evaluation')
                ->update(['type_of_evaluation' => $request->type_of_evaluation]);
        }

        return redirect()->route('users.edit', $user->uuid)->with('success', 'Usuario actualizado correctamente.');
    }

    public function sendEmailWelcome(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'error' => 'Usuario no encontrado'
            ], 404);
        }

        try {
            Mail::to($user->email)->send(new Welcome($user));
            return response()->json([
                'success' => 'Se ha enviado el correo',
                'data' => $user
            ], 200);
        } catch (\Throwable $e) {
            report($e);
            \Log::error("Error al enviar correo de bienvenida a {$user->email}: " . $e->getMessage());
            return response()->json([
                'error' => 'No se pudo enviar el correo por un fallo en el servicio de correo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $originalPassword = $user->password;
            $passwordRandom = Str::random(8);
            $user->password = Hash::make($passwordRandom);
            $user->save();

            try {
                Mail::to($user->email)->send(new resetPassword($user, $passwordRandom));
            } catch (\Throwable $e) {
                report($e);
                // Revertir contraseña si el envío falló
                $user->password = $originalPassword;
                $user->save();
                \Log::error("Error al enviar correo de restablecimiento de contraseña a {$user->email}: " . $e->getMessage());
                return redirect()->back()->with('error', 'No se pudo enviar el correo de recuperación debido a un fallo en el servicio de correo: ' . $e->getMessage());
            }
        }

        if ($request->type == 'reset_admin') {
            return redirect()->back()->with('success', 'Se ha enviado correctamente el correo de invitación.');
        } else {
            return redirect()->back()->with('success', 'Si el correo coincide con nuestro registro, se te enviara una contraseña.');
        }
    }

    public function destroy($uuid)
    {
        // Solo administradores pueden eliminar
        if (!Auth::user()->hasRole('administrator')) {
            return redirect()->back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $user = User::where('uuid', $uuid)->firstOrFail();

        // Verificar si tiene evaluaciones
        if ($user->account_id) {
            $assessments = $this->getAssessmentUser($user->account_id);
            
            if (!empty($assessments)) {
                return redirect()->back()->with('error', 'No es posible eliminar el usuario ya que tiene evaluaciones.');
            }
        }

        $user->delete();

        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }
}
