<?php

namespace App\Http\Controllers;

use App\Models\OpenAIConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OpenAIConfigController extends Controller
{
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
