<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Config;

class ApiController extends Controller
{
    public function getRespondents(){
        $config = Config::latest()->first();
        try {
            //Obtenemos el token
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'query { account(id: 243576) { respondents(first:100) { edges { node { id,firstName,lastName,email }}}}}',
            ]);

            $respondents = $authResponse->json('data.account.respondents.edges');
            return $respondents;
        
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function generateInterestsGraphReportData(){
        $tokenSession = session('api_token');

        if($tokenSession){
            // 2. Make Main API Request (with the token)
            $apiResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenSession,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'mutation($input: ReportWithAssessmentGenericInput!) {
                    generateInterestsGraphReportData(input: $input) {
                        orderedInterestsResults {
                            code,
                            displayName,
                            rawScore
                        }
                    }
                }',
                'variables' => [
                    'input' => [
                        'account' => 243514,
                        'assessment' => 942826,
                        'locale' => 'en-US',
                        'printerFriendly' => true,
                    ],
                ],
            ]);

            $result = $apiResponse->json();
            return $result;
        } else {
            echo 'El token no es válido';
        }
    }

    public function searchAssessment($id,$token){
        $tokenSession = session('api_token');

        if($tokenSession){
            $query = 'query ($input: AssessmentInput!) { 
                    assessment(input: $input) { 
                        id
                    } 
                }';
            $variables = [
                'input' => [
                    'id' => $id,
                    'token' => $token,
                    'language' => 'en-US'
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenSession,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.org/api/v1/questionnaire-data-collection', [
                'query' => $query,
                'variables' => $variables
            ]);

            $result = $response->json();
            return $result;

        } else {
            echo 'El token no es válido';
        }
    }

    public function startAssesment(){
        $tokenSession = session('api_token');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenSession,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.org/api/v1/questionnaire-data-collection', [
                'query' => 'mutation ($input: AssessmentStartInput!) {
                               assessment_start(input: $input) {
                                 id
                               }
                           }',
                'variables' => [
                    'input' => [
                        'id' => 119960,
                        'token' => 'd80f7754-d7a0-407e-b3cf-09cf240ed1a3',
                        'language' => 'en-US',
                        'ariFields' => []
                    ]
                ]
            ]);
        
            if ($response->successful()) {
                $data = $response->json();
                $assessmentId = $data['data']['assessment_start']['id'];

            } else {
                // Manejo de errores
                $error = $response->json()['errors'][0]['message'];
                dd("Error al iniciar la evaluación: $error");
                // Puedes mostrar un mensaje de error al usuario, etc.
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Manejo general de excepciones
        }
    }

    public function createSuperLink(){
        $tokenSession = session('api_token');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $tokenSession,
            'Content-Type' => 'application/json',
        ])->post('https://api.gr8pi.org/api/v1/questionnaire-data-collection', [
            'query' => 'mutation($input: CreateSuperLinkAssessmentInput!) {
                           createSuperLinkAssessment(input: $input) {
                               redirectUrl
                               assessmentId
                           }
                       }',
            'variables' => [
                'input' => [
                    'superLinkToken' => '4e9d948d-216c-4527-a6b0-6880a60561cf',
                    'email' => 'codingear@gmail.com',
                    'firstName' => 'Braulio',
                    'lastName' => 'Miramontes',
                    'gender' => 'M',
                    'preferredLanguage' => 'es-PR',
                ]
            ]
        ]);

        $data = $response->json();
        return $data;
    }
    
}
