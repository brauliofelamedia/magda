<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use App\Models\Config;
class ValidateToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $config = Config::where('id',1)->first();
        if(!$config){
            $this->getToken();
        } else {
            $this->checkToken($config->token);
        }
        
        return $next($request);
    }

    public function getToken(){

        //Datos para iniciar sesión
        $username = env('GR8PI_USERNAME');
        $password = env('GR8PI_PASSWORD');

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
            $config = Config::latest()->first();
            if(!$config){
                $config = new Config();
                $config->token = $token['token'];
                $config->refreshToken = $token['refreshToken'];
                $config->save();
            } else {
                $config->token = $token['token'];
                $config->refreshToken = $token['refreshToken'];
                $config->save();
            }
            
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkToken($token){
        try {
            //Obtenemos el token
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
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

    public function refreshToken(){
        $config = Config::latest()->first();
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
