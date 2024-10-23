<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Config;
use Illuminate\Support\Facades\Http;

class VerifyToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $config = Config::first();
        //Revisamos si hay token y refresh en $config
        if(!$config->token && !$config->refreshToken){
            $this->getToken();
        } else {
            $this->expirationToken();
        }

        return $next($request);
    }

    private function getToken(){
        //Datos para iniciar sesión
        $username = 'jorge@felamedia.com';
        $password = 'F3l@s0sa2024.';

        try {
            //Obtenemos el token
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'mutation($input:GenerateTokenInput!){generateToken(input:$input){token refreshToken userId}}',
                'variables' => [
                    'input' => [
                        'username' => $username,
                        'password' => $password,
                    ],
                ],
            ]);

            $token = $authResponse->json('data.generateToken');
            
            //Guardamos el token en tabla
            $config = Config::updateOrCreate(
                ['token' => $token['token']],
                ['refreshToken' => $token['refreshToken']]
            );
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function expirationToken(){

        $config = Config::first();
        try {
            //Obtenemos el token
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json',
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'query{viewer{id,email}}',
            ]);

            $data = $authResponse->json('data');

            //Si el token ya expiro, lo actualizamos
            if(!$data){
                $this->refreshToken();
            }

        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
         }
    }

    private function refreshToken(){
        $config = Config::first();
        try {
            //Obtenemos el token
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'mutation($input: String!) { refreshToken(refreshToken: $input) { token }}',
                'variables' => [
                    'input' => $config->refreshToken
                ],
            ]);

            $data = $authResponse->json('data.refreshToken');

            //Guardamos el nuevo token
            $config->token = $data['token'];
            $config->save();

        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
         }
    }
}
