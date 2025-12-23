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
        $results = [
            'dns_lookup' => [],
            'general_connectivity' => [],
            'api_connection' => [],
        ];

        try {
            // Test 1: DNS Resolution
            $host = 'api.gr8pi.com';
            $ip = gethostbyname($host);
            $dnsRecords = dns_get_record($host, DNS_A);
            
            $results['dns_lookup'] = [
                'host' => $host,
                'resolved_ip' => $ip,
                'dns_records' => $dnsRecords,
                'is_local' => ($ip === '127.0.0.1' || $ip === '::1') ? 'YES (Problem!)' : 'NO'
            ];

            // Test 2: General Internet Access (Google)
            try {
                $googleStart = microtime(true);
                $google = Http::withOptions(['connect_timeout' => 5])->get('https://www.google.com');
                $results['general_connectivity'] = [
                    'target' => 'www.google.com',
                    'status' => $google->status(),
                    'success' => $google->successful(),
                    'duration' => round(microtime(true) - $googleStart, 2) . 's'
                ];
            } catch (\Exception $e) {
                $results['general_connectivity'] = [
                    'target' => 'www.google.com',
                    'error' => $e->getMessage()
                ];
            }

            // Test 3: API Connection
            $username = 'jorge@felamedia.com';
            $password = 'F3l@s0sa2024.';
            
            $start = microtime(true);
            
            try {
                // Add User-Agent and explicitly disable some SSL checks for testing if needed
                $response = Http::withOptions(array_merge($this->getHttpOptions(), [
                    'debug' => false 
                ]))->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Laravel/TuTalentoFinder-Diagnostic',
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
                
                $results['api_connection'] = [
                    'status_code' => $response->status(),
                    'duration' => $duration . 's',
                    'success' => $response->successful(),
                    'response_preview' => $data
                ];
                
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
                    
                    $results['db_update'] = 'Success: Token updated in database.';
                    return response()->json(['status' => 'success', 'diagnostics' => $results]);
                } else {
                    $results['db_update'] = 'Skipped: API connection failed or returned no token.';
                    return response()->json(['status' => 'error', 'message' => 'API Connection Failed', 'diagnostics' => $results], 200);
                }

            } catch (\Exception $e) {
                 $results['api_connection'] = [
                    'status' => 'exception',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ];
                return response()->json(['status' => 'exception', 'message' => 'API Exception', 'diagnostics' => $results], 200);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fatal_exception',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'diagnostics' => $results
            ], 200);
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
