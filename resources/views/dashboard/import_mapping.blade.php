@extends('layouts.main')

@section('title','Mapeo de columnas para importación')

@push('css')
<style>
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
    
    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .mapping-instructions {
        background-color: #f8f9fa;
        border-left: 4px solid #033a60;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .mapping-title {
        font-weight: 600;
        color: #033a60;
        margin-bottom: 10px;
        font-size: 20px;
    }

    h3 {
        margin-bottom: 20px!important;
    }
    
    .required-field {
        color: red;
    }
    
    .mapping-card {
        border: 1px solid #e9e9e9;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

   ul {
        margin: 0;
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
                                <h3 class="text-center">Mapeo de columnas para importación</h3>
                                <div class="mapping-instructions">
                                    <h4 class="mapping-title">Instrucciones:</h4>
                                    <ul>
                                        <li>Revisa las columnas de tu archivo Excel.</li>
                                        <li>Selecciona en cada desplegable la columna que corresponde a cada campo requerido.</li>
                                        <li>Los campos marcados con <span class="required-field">*</span> son obligatorios.</li>
                                    </ul>
                                </div>

                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <div class="mapping-card">
                                    <form action="{{ route('dashboard.import.map') }}" method="POST">
                                        @csrf
                                        
                                        @foreach($required_columns as $field => $label)
                                        <div class="form-group mb-3">
                                            <label for="{{ $field }}">
                                                {{ $label }}
                                                @if($field != 'password')
                                                    <span class="required-field">*</span>
                                                @endif
                                            </label>
                                            <select name="mapping[{{ $field }}]" id="{{ $field }}" class="form-select" {{ $field != 'password' ? 'required' : '' }}>
                                                <option value="">Selecciona la columna correspondiente</option>
                                                @foreach($headers as $index => $header)
                                                    <option value="{{ $index }}">{{ $header }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endforeach
                                        
                                        <div class="text-center mt-4">
                                            <button type="submit" id="submitImport" class="btn btn-primary btn-submit">
                                                <i class="fa-solid fa-file-import"></i> <span id="buttonText">Completar importación</span>
                                                <span id="loadingSpinner" class="spinner-border spinner-border-sm ms-1 d-none" role="status" aria-hidden="true"></span>
                                            </button>
                                            <a href="{{ route('dashboard.import') }}" class="btn btn-cancel">
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
    </div>
@endsection

@push('js')
<script>
    // Script para detectar automáticamente las columnas basado en los nombres
    document.addEventListener('DOMContentLoaded', function() {
        const headers = @json($headers);
        const mapping = {
            'name': ['nombre', 'first name', 'first_name', 'firstname'],
            'last_name': ['apellido', 'apellidos', 'last name', 'last_name', 'lastname'],
            'email': ['correo', 'email', 'e-mail', 'correo electrónico', 'correo electronico'],
            'role': ['rol', 'role', 'perfil', 'tipo'],
            'gender': ['género', 'genero', 'sexo', 'gender'],
            'lang': ['idioma', 'language', 'lang', 'locale', 'idioma preferido'],
            'password': ['contraseña', 'password', 'clave', 'pass']
        };
        
        // Intenta hacer el mapeo automático
        Object.keys(mapping).forEach(field => {
            const possibleNames = mapping[field];
            headers.forEach((header, index) => {
                const headerLower = header.toLowerCase().trim();
                if (possibleNames.some(name => headerLower === name || headerLower.includes(name))) {
                    document.getElementById(field).value = index;
                }
            });
        });
        
        // Manejar el evento de envío del formulario
        const form = document.querySelector('form');
        const submitButton = document.getElementById('submitImport');
        const buttonText = document.getElementById('buttonText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        form.addEventListener('submit', function(e) {
            // Validar que todos los campos obligatorios estén seleccionados
            let isValid = true;
            const requiredSelects = document.querySelectorAll('select[required]');
            
            requiredSelects.forEach(select => {
                if (!select.value) {
                    isValid = false;
                }
            });
            
            if (isValid) {
                // Deshabilitar el botón y mostrar el spinner
                submitButton.disabled = true;
                buttonText.textContent = 'Procesando...';
                loadingSpinner.classList.remove('d-none');
            }
        });
    });
</script>
@endpush