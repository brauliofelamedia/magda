<?php
namespace App\Traits;
use App\Models\Config;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

trait APICalls
{
    public function getRespondents()
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'query { account(id: 243576) { respondents(first:100) { edges { node { id,firstName,lastName,email,locale }}}}}',
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

    public function startEvaluation($id,$token,$lang)
    {
        $config = Config::latest()->first();

        try {
            $authResponse = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type'  => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-data-collection', [
                'query'     => 'mutation($input: AssessmentStartInput!) { assessment_start(input: $input) { id status } }',
                'variables' => [
                    'input' => [
                        'id'       => $id,
                        'token'    => $token,
                        'language' => $lang
                    ]
                ]
            ]);

        $data = $authResponse->json();
        $id_return = $data['data']['assessment_start']['id'];
        return $id_return;

        //return redirect()->route('assessments.continue',[$id,$token,$lang]);

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function createNewEvaluation($respondentId,$locale,array $emails)
    {
        $dateNow = Carbon::now();
        $dateNextWeek = $dateNow->addWeek();

        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type'  => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query'     => 'mutation($input: CreateAssessmentInput!) { createAssessment(input: $input) { assessment { id } }}',
                'variables' => [
                    'input' => [
                        //Tu Talento Finder Intereses - 261966
                        //Tu Talento Finder Full Version - 261967
                        'assessmentTemplateId' => 261966,
                        'respondentId'         => $respondentId,
                        'accountId'           => 243576,
                        'locale'              => $locale,
                        'expirationTime'      => $dateNextWeek,
                        'subscribed'          => true,
                        'assessmentOptions'    => [
                            'sendReportsWhenAssessmentIsComplete' => true,
                            'notificationEmails'                 => $emails,
                            'reportTypes'                        => ['INDIVIDUAL', 'SUMMARY'],
                            'reportPreferLocale'                 => $locale,
                            'jobProfileIdForSingleJobProfileReports' => "",
                            'jobProfileIdsForMultipleJobProfileReports' => "",
                            'sendingIndividualReport'             => true,
                            'printerFriendlyReport'               => true
                        ]
                    ]
                ]
            ]);

            $data = $authResponse->json();
            return $data['data']['createAssessment']['assessment']['id'];

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
                        'accountId' => 243576,
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

    public function getAssesment($assessmentId)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => '
                    query getAssessmentDetails($assessmentId: ID!, $accountId: ID!) {
                        account(id: $accountId) {
                            assessment(id: $assessmentId) {
                                id
                                token
                                status
                                template {
                                    id
                                    title
                                    behaviorDimensions
                                    cognitiveDimensions
                                    interestsDimensions
                                    inviteTemplateId
                                    thankYouTemplateId
                                }
                            }
                        }
                    }
                ',
                'variables' => [
                    'assessmentId' => $assessmentId,
                    'accountId' => 243576,
                ]
            ]);

            $assessment = $authResponse->json('data.account.assessment');
            return $assessment;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function closeAnswers($assessmentId,$token)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-data-collection', [
                'query'=> 'mutation($input: AssessmentSubmitInput!){ assessment_submit(input: $input) {id}}',
                "variables" => [
                    "input" => [
                        "id"=> $assessmentId,
                        "token" => $token
                    ]
                ]
            ]);

            $data = $authResponse->json('data.assessment_submit.id');
            return $data;

        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    public function updateAnswers($assessmentId,$token,$responses)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-data-collection', [
                'query' => '
                    mutation($input: SubmitAnswersInput!) {
                        assessment_submitAnswers(input: $input)
                    }
                ',
                'variables' => [
                    'input' => [
                        'id' => $assessmentId,
                        'token' => $token,
                        'answers' => $responses
                    ]
                ]
            ]);
            $data = $authResponse->json('data');
            return $data;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAssessmentUser($respondentId)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type'  => 'application/json'
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
                    'accountId'    => 243576,
                    'respondentId' => $respondentId
                ]
            ]);

            $assessments = $authResponse->json('data.account.respondent.timeline.edges');
            return $assessments;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getDataAssessment($id,$token,$lang)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-data-collection', [
                'query' => 'query($input: AssessmentInput!) {
                assessment(input: $input) {
                    id
                    content {
                    behavior {
                        id
                        displayName
                        instructions
                        groups {
                        name
                        items {
                            id
                            text
                            description
                            answers {
                            id
                            text
                            }
                        }
                        }
                    }
                    cognitive {
                        id
                        displayName
                        instructions
                        groups {
                        name
                        items {
                            id
                            text
                            description
                            answers {
                            id
                            text
                            }
                        }
                        }
                    }
                    interests {
                        id
                        displayName
                        instructions
                        groups {
                        name
                        items {
                            id
                            text
                            description
                            answers {
                            id
                            text
                            }
                        }
                        }
                    }
                    }
                }
                }',
                'variables' => [
                    'input' => [
                        'id' => $id,
                        'token' => $token,
                        'language' => $lang
                    ]
                ]
            ]);

            //Apunta al contenido que incluye "behavior,cognitive,interests".
            $assessmentData = $authResponse->json('data.assessment.content');

            return $assessmentData;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function createUser($firstName,$lastName,$email,$gender,$locale)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type'  => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query'     => 'mutation ($input: CreateRespondentInput!) { createRespondent(input: $input) { respondent { id }}}',
                'variables' => [
                    'input' => [
                        'firstName'  => $firstName,
                        'lastName'   => $lastName,
                        'email'      => $email,
                        'gender'     => $gender,
                        'locale'     => $locale,
                        'targetJobId' => null,
                        'type'       => 'EXTERNAL',
                        'accountId'  => 243576,
                    ]
                ]
            ]);

            $data = $authResponse->json();
            $userId = $authResponse->json('data.createRespondent.respondent.id');
            $errors = $authResponse->json('errors');

            /*$data = [
                'errors' => $errors[0]['message'],
                'userId' => $userId
            ];*/
            return $data;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    //Obtener el reporte de una evaluación
    public function getReportAssessment($respondentId,$locale)
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
                        'account' => 243576,
                        'assessment' => $respondentId,
                        'locale' => $locale,
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

    public function getReportAssessmentPDF($respondentId,$locale)
    {
        $config = Config::latest()->first();
        try {
            $authResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type' => 'application/json'
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'mutation($input: ReportWithAssessmentGenericInput!) { generateStudentInterestsReport(input: $input) { url }}',
                'variables' => [
                    'input' => [
                        'account' => 243576,
                        'assessment' => $respondentId,
                        'locale' => $locale,
                        'printerFriendly' => true
                    ]
                ]
            ]);

            $reports = $authResponse->json('data.generateStudentInterestsReport.url');
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

    //Reports
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
