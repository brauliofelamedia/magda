<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Config;

class TokenController extends Controller
{
    private function getHttpOptions()
    {
        return [
            'connect_timeout' => 30,
            'timeout' => 60,
            'verify' => false,
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ]
        ];
    }

    public function testConnection()
    {
        try {
            // Attempt full login
            $username = 'jorge@felamedia.com';
            $password = 'F3l@s0sa2024.';
            
            $start = microtime(true);
            
            $response = Http::withOptions($this->getHttpOptions())->withHeaders([
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
            
            $duration = round(microtime(true) - $start, 2);
            $data = $response->json();
            
            if ($response->successful() && isset($data['data']['generateToken']['token'])) {
                // Update DB
                $tokenData = $data['data']['generateToken'];
                $config = Config::first();
                if ($config) {
                    $config->token = $tokenData['token'];
                    $config->refreshToken = $tokenData['refreshToken'];
                    $config->save();
                } else {
                    Config::create([
                        'token' => $tokenData['token'],
                        'refreshToken' => $tokenData['refreshToken']
                    ]);
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Connection successful and database updated.',
                    'duration_seconds' => $duration,
                    'token_preview' => substr($tokenData['token'], 0, 10) . '...',
                    'config_updated_at' => $config ? $config->updated_at->toDateTimeString() : 'created now'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'API call failed.',
                    'duration_seconds' => $duration,
                    'response' => $data,
                    'status_code' => $response->status()
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'exception',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}

    public function getToken(){

        //Datos para iniciar sesión
        $username = 'jorge@felamedia.com';
        $password = 'F3l@s0sa2024.';

        try {
            //Obtenemos el token
            $authResponse = Http::withOptions($this->getHttpOptions())->withHeaders([
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
            $config = Config::first();
            if ($config) {
                $config->token = $token['token'];
                $config->refreshToken = $token['refreshToken'];
                $config->save();
            } else {
                $config = Config::create([
                    'token' => $token['token'],
                    'refreshToken' => $token['refreshToken']
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function expirationToken(){

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

    public function refreshToken(){
        $config = Config::first();
        try {
            //Obtenemos el token
            $authResponse = Http::withOptions($this->getHttpOptions())->withHeaders([
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
