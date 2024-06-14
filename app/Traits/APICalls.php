<?php
namespace App\Traits;
use App\Models\Config;
use Illuminate\Support\Facades\Http;

trait APICalls
{
    //296 pagina
    public function getRespondents()
    {
        $config = Config::latest()->first();
        try {
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

    public function getUserEvaluation()
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => '{ 
                    account(id: 243576) { 
                        respondents(first: 100, after: "CURSOR") { 
                            edges { 
                                node { 
                                    id 
                                    firstName 
                                    lastName 
                                    email 
                                }
                                cursor 
                            }
                            pageInfo { 
                                startCursor 
                                endCursor 
                                hasPreviousPage 
                                hasNextPage 
                            }
                            totalCount
                            indexFrom
                            indexTo
                        } 
                    } 
                }'
            ]);

            $respondents = $authResponse->json('data.account.respondents.edges');
            return $respondents;
        
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function sendEmailEvaluation($assessmentId)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'mutation($input: InviteRespondentTakeAssessmentInput!) { inviteRespondentTakeAssessment(input: $input) { assessment { id } } }',
                'variables' => [
                    'input' => [
                        'accountId' => env('MAGDA_USER_ID'),
                        'assessmentId' => $assessmentId
                    ]
                ]
            ]);

            $assessments = $authResponse->json('data.inviteRespondentTakeAssessment.assessment.id');
            return $assessments;
        
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getAssessmentUser($respondentId)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'query($accountId: ID!, $respondentId: ID!) {
                               account(id: $accountId) {
                                   respondent(id: $respondentId) {
                                       timeline {
                                           edges {
                                               node {
                                                   ... on Assessment {
                                                       id
                                                       token
                                                       locale
                                                       startedOn
                                                       submittedOn
                                                       status
                                                   }
                                               }
                                           }
                                       }
                                   }
                               }
                           }',
                'variables' => [
                    'accountId' => 243576,
                    'respondentId' => $respondentId
                ]
            ]);

            $assessments = $authResponse->json('data.account.respondent.timeline.edges');
            return $assessments;
        
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    //Obtener el reporte de una evaluación
    public function getReportAssessment($respondentId)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'mutation($input: ReportWithAssessmentGenericInput!) { generateInterestsGraphReportData(input: $input) { orderedInterestsResults {  code,displayName,rawScore }}}',
                'variables' => [
                    'input' => [
                        'account' => env('MAGDA_USER_ID'),
                        'assessment' => $respondentId,
                        'locale' => 'en-US',
                        'printerFriendly' => true
                    ]
                ]
            ]);

            $reports = $authResponse->json('data.generateInterestsGraphReportData.orderedInterestsResults');
            return $reports;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getTemplatesEvaluation()
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => '{ 
                    account(id: 243576) { 
                        assessmentTemplates { 
                            edges { 
                                node { 
                                    id
                                    title 
                                } 
                            } 
                        } 
                    } 
                }'
            ]);

            $respondents = $authResponse->json('data.account.respondents.edges');
            return $respondents;
        
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function generateReportIndividual($idEvaluation)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'operationName' => 'generateIndividualReport',
                'variables' => [
                    'input' => [
                        'account' => "243576",
                        'assessment' => $idEvaluation,
                        'locale' => "es-ES",
                        'printerFriendly' => true
                    ]
                ],
                'query' => 'mutation generateIndividualReport($input: ReportWithAssessmentGenericInput!) { generateIndividualReport(input: $input) { url } }'
            ]);
            
            if ($authResponse->successful()) {
                $responseData = $authResponse->json();
                $reportUrl = $responseData['data']['generateIndividualReport']['url'];
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function sendSuperLink($emails,$idTemplate)
    {
        //261967 - Tu Talento Finder Full Version
        //261966 - Tu Talento Finder Intereses
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'operationName' => 'createSuperLink',
                'variables' => [
                    'input' => [
                        'name' => 'Nombre del Superlink', 
                        'description' => 'Descripción del Superlink', 
                        'notes' => 'Notas adicionales (opcional)',
                        'assessmentTemplateId' => $idTemplate,
                        'accountId' => '243576',
                        'respondentType' => 'EXTERNAL', // O 'EXTERNAL' según corresponda
                        'targetJobId' => 'ID_DE_PERFIL_DE_TRABAJO_OBJETIVO', // Opcional
                        'notificationEmails' => $emails,
                        'reports' => ['INDIVIDUAL'], // Puedes agregar otros tipos de reporte
                        'locale' => 'es-ES', // O el código de idioma que necesites
                        'sendRespondentReport' => true, // O false, según tus preferencias
                        'sendRespondentCcInterestsReport' => false, // O true, según tus preferencias
                        'sendRespondentCcPersonalityReport' => false, // O true, según tus preferencias
                        'printFriendlyReport' => true // O true, según tus preferencias
                    ]
                ],
                'query' => 'mutation createSuperLink($input: CreateSuperLinkInput!) { superLink(input: $input) { id } }'
            ]);

            $respondents = $authResponse->json('data');
            return $respondents;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function sendUserInvitation($email,$questionary)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'operationName' => 'scheduleQuestionnaire',
                'variables' => [
                    'input' => [
                        'respondentEmails' => [$email],
                        'questionnaireId' => $questionary
                    ]
                ],
                'query' => 'mutation scheduleQuestionnaire($input: ScheduleQuestionnaireInput!) { scheduleQuestionnaire(input: $input) { id } }'
            ]);

            $respondents = $authResponse->json('data.account.respondents.edges');
            return $respondents;

            if ($authResponse->successful()) {
                $responseData = $authResponse->json();
                $scheduleId = $responseData['data']['scheduleQuestionnaire']['id'];
            }
        
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}