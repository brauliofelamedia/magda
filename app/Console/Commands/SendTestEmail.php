<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Mail\resetPassword;
use App\Mail\SendCreateUser;
use App\Mail\AssignEvaluate;
use App\Models\User;
use Exception;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test 
                            {email : La dirección de correo electrónico para enviar los mensajes de prueba}
                            {--tipo=todos : El tipo de correo a enviar (test, reset, create, evaluate, todos)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía correos electrónicos de prueba a la dirección especificada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $tipo = $this->option('tipo');
        
        // Crear un usuario ficticio para las pruebas
        $mockUser = new User();
        $mockUser->name = 'Usuario de Prueba';
        $mockUser->email = $email;
        $mockUser->name_institution = 'Institución de Prueba';
        
        // Contraseña de prueba
        $mockPassword = 'Contraseña123!';
        
        // URL de prueba para evaluación
        $mockUrl = config('app.url') . '/evaluacion/123';
        
        $this->info("Enviando correo(s) de prueba a: {$email}");
        
        try {
            if ($tipo === 'todos' || $tipo === 'test') {
                Mail::to($email)->send(new TestMail());
                $this->info('✓ Correo de prueba básico enviado correctamente.');
            }
            
            if ($tipo === 'todos' || $tipo === 'reset') {
                Mail::to($email)->send(new resetPassword($mockUser, $mockPassword));
                $this->info('✓ Correo de restablecimiento de contraseña enviado correctamente.');
            }
            
            if ($tipo === 'todos' || $tipo === 'create') {
                Mail::to($email)->send(new SendCreateUser($mockUser, $mockPassword));
                $this->info('✓ Correo de creación de usuario enviado correctamente.');
            }
            
            if ($tipo === 'todos' || $tipo === 'evaluate') {
                Mail::to($email)->send(new AssignEvaluate($mockUser, $mockUrl));
                $this->info('✓ Correo de asignación de evaluación enviado correctamente.');
            }
            
            $this->info('¡Todos los correos de prueba han sido enviados correctamente!');
        } catch (Exception $e) {
            $this->error('Error al enviar correo(s) de prueba:');
            $this->error($e->getMessage());
            return 1;
        }
        
        return 0;
    }
}