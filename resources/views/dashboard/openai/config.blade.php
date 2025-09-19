@extends('layouts.main')

@section('title','Configuración de OpenAI')

@push('css')
<style>
    .box-inner {
        z-index: 1!important;
    }

    .btn-submit {
        padding: 15px 25px !important;
        font-size: 16px !important;
        font-weight: bold !important;
        background-color: #033a60 !important;
        border: 0 !important;
    }

    .btn-submit:hover {
        background-color: #035f9f !important;
    }
    
    .config-instructions {
        background-color: #f8f9fa;
        border-left: 4px solid #033a60;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .instruction-title {
        font-weight: 600;
        color: #033a60;
    }

    p {
        font-size: 16px!important;
    }
    
    .instruction-step {
        margin-bottom: 13px;
    }

    label {
        font-weight: bold;
    }

    .btn-cancel {
        padding: 15px 25px !important;
        font-size: 16px !important;
        font-weight: bold !important;
        background-color:rgb(124, 124, 124) !important;
        color: white;
        border: 0 !important;
    }

    .btn-cancel:hover {
        color: white;
        background-color: rgb(124, 124, 124) !important;
    }

    #dashboard {
        margin-bottom: 50px;
    }
    
    .api-key-field {
        font-family: monospace;
        letter-spacing: 1px;
    }
</style>
@endpush

@section('content')

    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-2">
            <div class="col-12">
                <div class="box">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Configuración de API Key de OpenAI</h3>
                                
                                <div class="config-instructions">
                                    <p class="instruction-title">Instrucciones:</p>
                                    <p class="instruction-step">1. Ingrese su API Key de OpenAI para utilizar en las consultas de IA dentro del sistema.</p>
                                    <p class="instruction-step">2. La API Key se almacenará de forma segura en la base de datos.</p>
                                    <p class="instruction-step">3. Si no se proporciona una API Key personalizada, el sistema utilizará la configurada por defecto en el archivo de entorno.</p>
                                    
                                    <div class="alert alert-info mt-3 mb-3">
                                        <strong>Nota importante:</strong>
                                        <p class="mb-0">La API Key se utilizará para todas las consultas que requieran servicios de OpenAI.</p>
                                    </div>
                                </div>

                                @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                                @endif

                                @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                                @endif

                                <form action="{{ route('openai.config.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-4">
                                        <label for="api_key">API Key de OpenAI:</label>
                                        <input type="text" class="form-control api-key-field" name="api_key" id="api_key" required 
                                               value="{{ $apiKey }}" placeholder="sk-...">
                                        @error('api_key')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-submit">
                                            <i class="fa-solid fa-save"></i> Guardar configuración
                                        </button>
                                        <a href="{{ route('dashboard.welcome') }}" class="btn btn-cancel">
                                            <i class="fa-solid fa-arrow-left"></i> Volver
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Opcional: Añadir lógica JavaScript si es necesario
    });
</script>
@endpush