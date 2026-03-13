<?php

namespace App\Http\Controllers;

use App\Models\OpenAIConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OpenAIConfigController extends Controller
{
    private function redactOpenAIError(string $message, ?string $apiKey = null): string
    {
        if (!empty($apiKey)) {
            $message = str_replace($apiKey, '[REDACTED]', $message);
        }

        return preg_replace('/sk-[A-Za-z0-9_-]{10,}/', 'sk-***', $message) ?? $message;
    }

    /**
     * Display the OpenAI configuration form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $config = OpenAIConfig::where('active', true)->latest()->first();
        $apiKey = $config ? $config->api_key : null;
        
        return view('dashboard.openai.config', compact('apiKey'));
    }

    /**
     * Store or update the API key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string|min:10',
        ]);

        try {
            $client = \OpenAI::client($request->api_key);
            $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => 'Responde solo con OK.'],
                ],
                'max_tokens' => 5,
            ]);
        } catch (\Throwable $e) {
            Session::flash('error', 'No se pudo validar la API Key con OpenAI. ' . $this->redactOpenAIError($e->getMessage(), $request->api_key));
            return redirect()->back()->withInput();
        }

        // Desactivar todas las configuraciones existentes
        OpenAIConfig::where('active', true)->update(['active' => false]);

        // Crear una nueva configuración
        OpenAIConfig::create([
            'api_key' => $request->api_key,
            'created_by' => Auth::id(),
            'active' => true,
        ]);

        Session::flash('success', 'La API Key de OpenAI se ha configurado correctamente.');
        return redirect()->route('openai.config');
    }
}
