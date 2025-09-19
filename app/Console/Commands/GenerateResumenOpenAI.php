<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateResumenOpenAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessments:generate-resumen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate resumen_openia for existing assessments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $assessments = Assessment::whereNotNull('openia')
                               ->whereNull('resumen_openia')
                               ->get();

        $count = $assessments->count();
        $this->info("Found {$count} assessments that need summaries.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($assessments as $assessment) {
            $resumenOpenAI = $this->generateResumenOpenAI($assessment->openia);
            $assessment->resumen_openia = $resumenOpenAI;
            $assessment->save();
            
            $bar->advance();
            // Adding a small delay to avoid OpenAI API rate limits
            sleep(1);
        }

        $bar->finish();
        $this->newLine();
        $this->info('All summaries generated successfully.');
    }

    private function generateResumenOpenAI($openiaContent) {
        $prompt = "Basándote en:

Los tres intereses ocupacionales más altos del participante (en orden de prioridad).
Las descripciones detalladas de esos tipos de interés.
Las ocupaciones sugeridas en las categorías profesionales del informe.
La compatibilidad porcentual si está incluida.
Los pasatiempos y motivadores asociados a los intereses dominantes.
Genera lo siguiente:

Las **5 profesiones ideales** para el participante, al día de hoy, que estén alineadas con sus intereses, motivadores y nivel de preparación actual.
Las **5 mejores ideas de emprendimiento** que podrían entusiasmar y retar al participante.
Justifica brevemente cada recomendación (1-2 líneas por cada profesión o emprendimiento).
IMPORTANTE: Las recomendaciones deben ser prácticas y relevantes al contexto actual del mercado laboral.
Este es el informe para analizar: generalo en html solo entregame el body, no pongas fechas, sin header ordenando la lista, parrafos y titulos principal (h1), titulos secundarios (h2) y otros titulo (h3).

" . $openiaContent;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);
        
        return $response->choices[0]->message->content;
    }
}