<?php
namespace App\Traits;
use App\Models\Config;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

trait APICalls
{
    public function getRespondents()
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'query { account(id: 243576) { respondents(first:100) { edges { node { id,firstName,lastName,email,locale }}}}}'
            );

            if (is_string($data)) {
                return [];
            }

            if (isset($data['errors'])) {
                return [];
            }

            return $data['data']['account']['respondents']['edges'] ?? [];

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getUserEvaluation()
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                '{
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
            );

            if (is_string($data)) {
                return [];
            }

            return $data['data']['account']['respondents']['edges'] ?? [];

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function startEvaluation($id,$token,$lang)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-data-collection',
                'mutation($input: AssessmentStartInput!) { assessment_start(input: $input) { id status } }',
                [
                    'input' => [
                        'id'       => $id,
                        'token'    => $token,
                        'language' => $lang
                    ]
                ]
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data']['assessment_start']['id'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function createNewEvaluation($respondentId,$locale,array $emails,$type)
    {
        $dateNow = Carbon::now();
        $dateNextWeek = $dateNow->addWeek();

        if($type == 'short'){
            $idTemplate = 261966;
        } else {
            $idTemplate = 261967;
        }

        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation($input: CreateAssessmentInput!) { createAssessment(input: $input) { assessment { id } }}',
                [
                    'input' => [
                        //Tu Talento Finder Intereses - 261966
                        //Tu Talento Finder Full Version - 261967
                        'assessmentTemplateId' => $idTemplate,
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
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data']['createAssessment']['assessment']['id'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function sendEmailEvaluation($assessmentId)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation($input: InviteRespondentTakeAssessmentInput!) { inviteRespondentTakeAssessment(input: $input) { assessment { id } } }',
                [
                    'input' => [
                        'accountId' => 243576,
                        'assessmentId' => $assessmentId
                    ]
                ]
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data']['inviteRespondentTakeAssessment']['assessment']['id'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getAssesment($assessmentId)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                '
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
                [
                    'assessmentId' => $assessmentId,
                    'accountId' => 243576,
                ]
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data']['account']['assessment'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function closeAnswers($assessmentId,$token)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-data-collection',
                'mutation($input: AssessmentSubmitInput!){ assessment_submit(input: $input) {id}}',
                [
                    "input" => [
                        "id"=> $assessmentId,
                        "token" => $token
                    ]
                ]
            );

            if (is_string($data)) {
                return $data;
            }

            return $data['data']['assessment_submit']['id'] ?? null;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateAnswers($assessmentId,$token,$responses)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-data-collection',
                '
                    mutation($input: SubmitAnswersInput!) {
                        assessment_submitAnswers(input: $input)
                    }
                ',
                [
                    'input' => [
                        'id' => $assessmentId,
                        'token' => $token,
                        'answers' => $responses
                    ]
                ]
            );

            if (is_string($data)) {
                return $data;
            }

            return $data['data'] ?? null;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAssessmentUser($respondentId)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'query($accountId: ID!, $respondentId: ID!) {
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
                [
                    'accountId'    => 243576,
                    'respondentId' => $respondentId
                ]
            );

            if (is_string($data)) {
                return [];
            }

            return $data['data']['account']['respondent']['timeline']['edges'] ?? [];

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getDataAssessment($id,$token,$lang)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-data-collection',
                'query($input: AssessmentInput!) {
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
                [
                    'input' => [
                        'id' => $id,
                        'token' => $token,
                        'language' => $lang
                    ]
                ]
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data']['assessment']['content'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function createUser($firstName,$lastName,$email,$gender,$locale)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation ($input: CreateRespondentInput!) { createRespondent(input: $input) { respondent { id }}}',
                [
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
            );

            return $data;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getReportAssessment($respondentId,$locale)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation($input: ReportWithAssessmentGenericInput!) { generateInterestsGraphReportData(input: $input) { orderedInterestsResults {  code,displayName,rawScore }}}',
                [
                    'input' => [
                        'account' => 243576,
                        'assessment' => $respondentId,
                        'locale' => $locale,
                        'printerFriendly' => true
                    ]
                ]
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data']['generateInterestsGraphReportData']['orderedInterestsResults'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getReportInterestPDF($respondentId,$locale)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation($input: ReportWithAssessmentGenericInput!) { generateStudentInterestsReport(input: $input) { url }}',
                [
                    'input' => [
                        'account' => 243576,
                        'assessment' => $respondentId,
                        'locale' => $locale,
                        'printerFriendly' => true
                    ]
                ]
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data']['generateStudentInterestsReport']['url'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getReportAssessmentPDF($respondentId, $locale = 'en-US')
    {
        $reports = [];

        try {
            // Get Interests Report
            $interestsData = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation($input: ReportWithAssessmentGenericInput!) { generateCareerCounselingInterestsReport(input: $input) { url }}',
                [
                    'input' => [
                        'account' => 243576,
                        'assessment' => $respondentId,
                        'locale' => $locale,
                        'printerFriendly' => true
                    ]
                ]
            );
            $reports['interests'] = isset($interestsData['data']) ? ($interestsData['data']['generateCareerCounselingInterestsReport']['url'] ?? null) : null;

            // Get Personality Report
            $personalityData = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation($input: ReportWithAssessmentAndJobProfileInput!) { generateSummaryReport(input: $input) { url }}',
                [
                    'input' => [
                        'account' => 243576,
                        'assessment' => $respondentId,
                        'locale' => $locale,
                        'printerFriendly' => true
                    ]
                ]
            );
            $reports['summary'] = isset($personalityData['data']) ? ($personalityData['data']['generateSummaryReport']['url'] ?? null) : null;

            // Get Individual Report
            $individualData = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation($input: ReportWithAssessmentGenericInput!) { generateIndividualReport(input: $input) { url }}',
                [
                    'input' => [
                        'account' => 243576,
                        'assessment' => $respondentId,
                        'locale' => $locale,
                        'printerFriendly' => true
                    ]
                ]
            );
            $reports['individual'] = isset($individualData['data']) ? ($individualData['data']['generateIndividualReport']['url'] ?? null) : null;

            return $reports;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getTemplatesEvaluation()
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                '{
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
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data']['account']['respondents']['edges'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function sendSuperLink($emails,$idTemplate)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation createSuperLink($input: CreateSuperLinkInput!) { superLink(input: $input) { id } }',
                [
                    'input' => [
                        'name' => 'Nombre del Superlink',
                        'description' => 'Descripción del Superlink',
                        'notes' => 'Notas adicionales (opcional)',
                        'assessmentTemplateId' => $idTemplate,
                        'accountId' => '243576',
                        'respondentType' => 'EXTERNAL',
                        'targetJobId' => 'ID_DE_PERFIL_DE_TRABAJO_OBJETIVO',
                        'notificationEmails' => $emails,
                        'reports' => ['INDIVIDUAL'],
                        'locale' => 'es-ES',
                        'sendRespondentReport' => true,
                        'sendRespondentCcInterestsReport' => false,
                        'sendRespondentCcPersonalityReport' => false,
                        'printFriendlyReport' => true
                    ]
                ]
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function sendUserInvitation($email,$questionary)
    {
        try {
            $data = $this->executeGraphQL(
                'https://api.gr8pi.com/api/v1/questionnaire-scheduling',
                'mutation scheduleQuestionnaire($input: ScheduleQuestionnaireInput!) { scheduleQuestionnaire(input: $input) { id } }',
                [
                    'input' => [
                        'respondentEmails' => [$email],
                        'questionnaireId' => $questionary
                    ]
                ]
            );

            if (is_string($data)) {
                return null;
            }

            return $data['data']['account']['respondents']['edges'] ?? null;

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Executes a GraphQL request with automatic token refresh on authentication failure.
     *
     * @param string $url
     * @param string $query
     * @param array $variables
     * @return array|string Response data array or error string
     */
    private function executeGraphQL($url, $query, $variables = [])
    {
        $config = Config::latest()->first();
        
        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $config->token,
                'Content-Type'  => 'application/json'
            ])->post($url, [
                'query'     => $query,
                'variables' => $variables
            ]);

            $data = $response->json();
            
            // Check for authentication errors
            $isUnauthenticated = false;
            
            if ($response->status() === 401) {
                $isUnauthenticated = true;
            } elseif (isset($data['errors'])) {
                foreach ($data['errors'] as $error) {
                    if (isset($error['message']) && (
                        stripos($error['message'], 'Unauthenticated') !== false || 
                        stripos($error['message'], 'Invalid token') !== false ||
                        stripos($error['message'], 'Expired token') !== false
                    )) {
                        $isUnauthenticated = true;
                        break;
                    }
                }
            }

            if ($isUnauthenticated) {
                if ($this->attemptRefreshToken()) {
                    $config = Config::latest()->first();
                    $response = Http::withHeaders([
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $config->token,
                        'Content-Type'  => 'application/json'
                    ])->post($url, [
                        'query'     => $query,
                        'variables' => $variables
                    ]);
                    return $response->json();
                }
            }

            return $data;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Attempts to refresh the access token using the refresh token.
     *
     * @return bool
     */
    private function attemptRefreshToken()
    {
        $config = Config::latest()->first();
        
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post('https://api.gr8pi.com/api/v1/questionnaire-scheduling', [
                'query' => 'mutation($input: String!) { refreshToken(refreshToken: $input) { token }}',
                'variables' => [
                    'input' => $config->refreshToken
                ],
            ]);

            $data = $response->json('data.refreshToken');
            
            if (isset($data['token'])) {
                $config->token = $data['token'];
                $config->save();
                return true;
            }
            
            return false;

        } catch(\Exception $e) {
            return false;
        }
    }
}
