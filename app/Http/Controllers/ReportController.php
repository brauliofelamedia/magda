<?php

namespace App\Http\Controllers;
use App\Traits\APICalls;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;

class ReportController extends Controller
{
    use APICalls;

    private function analyzePDFWithOpenAI($pdfText) {
        // Verificar si hay una API key personalizada configurada
        $apiKey = \App\Models\OpenAIConfig::getApiKey();
        $client = OpenAI::client($apiKey);
        
        $prompt = "Basado en el siguiente texto que contiene intereses y habilidades profesionales, 
                  sugiere 5 trabajos actualizados y relevantes en el mercado laboral actual, enfocate en la actualidad nada de vacantes viejas / antiguas. 
                  Para cada trabajo, explica brevemente por qué sería una buena opción y dame un texto de introduccion principal antes de los 5 trabajos:\n\n" . $pdfText;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);
        
        return $response->choices[0]->message->content;
    }

    public function getReportAssessments(Request $request)
    {
        try {
            $data = $this->getReportAssessment($request->id, $request->locale);
            $pdfUrl = $this->getReportAssessmentPDF($request->id, $request->locale);

            if (!filter_var($pdfUrl, FILTER_VALIDATE_URL)) {
                throw new Exception('Invalid PDF URL');
            }

            // Configurar contexto para el stream con timeout
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'max_redirects' => 2
                ]
            ]);

            $pdfContent = @file_get_contents($pdfUrl, false, $context);
            
            if ($pdfContent === false) {
                throw new Exception('Failed to download PDF');
            }

            // Crear instancia del parser con config optimizada
            $parser = new Parser();
            
            // Parsear PDF con manejo de memoria mejorado
            $pdf = $parser->parseContent($pdfContent);
            
            // Liberar memoria
            unset($pdfContent);
            
            // Extraer texto de todas las páginas
            $text = '';
            foreach ($pdf->getPages() as $page) {
                $text .= $page->getText() . "\n";
            }
            
            // Limpiar el texto
            $text = trim(preg_replace('/\s+/', ' ', $text));

            // Analizar el texto con OpenAI
            $aiAnalysis = $this->analyzePDFWithOpenAI($text);

            return response()->json([
                'success' => true,
                'data' => $data,
                'pdfUrl' => $pdfUrl,
                'pdfText' => $text,
                'aiAnalysis' => $aiAnalysis
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'errorType' => get_class($e)
            ], 500);
        }
    }
}
