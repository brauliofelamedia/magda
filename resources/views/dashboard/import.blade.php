@extends('layouts.main')

@section('title','Importar usuarios')

@push('css')
<style>
    .example-file {
        color: #f74219;
        text-decoration: underline;
        text-align: center;
        display: block;
    }

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
    
    .import-instructions {
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
                                <h3 class="text-center">Importación de usuarios</h3>
                                
                                <div class="import-instructions">
                                    <p class="instruction-title">Instrucciones:</p>
                                    <p class="instruction-step">1. Prepare un archivo Excel con los datos de usuarios a importar.</p>
                                    <p class="instruction-step">2. El archivo debe incluir columnas para: <strong>nombre, apellidos, correo, género (female o male) e idioma preferido (es, en, etc)</strong>.</p>
                                    
                                    @if(Auth::user()->hasRole('administrator'))
                                    <p class="instruction-step">3. Como administrador, puede especificar el <strong>rol</strong> del usuario (institution, respondent, etc.) y el <strong>correo de la institución</strong> a la que asignar el usuario.</p>
                                    <p class="instruction-step"><strong>Nota:</strong> El sistema buscará la institución por su correo electrónico registrado en el sistema.</p>
                                    
                                    <div class="alert alert-info mt-3 mb-3">
                                        <strong>Plantillas disponibles:</strong>
                                        <ul class="mt-2 mb-0">
                                            <li><strong>Plantilla para administradores:</strong> Incluye todas las columnas, incluido rol y correo de institución.</li>
                                            <li><strong>Plantilla para instituciones:</strong> Versión simplificada sin columnas de rol o institución.</li>
                                        </ul>
                                    </div>
                                    @else
                                    <p class="instruction-step"><strong>Nota:</strong> Todos los usuarios importados tendrán automáticamente el rol "respondent" y se asignarán a su institución.</p>
                                    @endif
                                    
                                    <p class="instruction-step">3. La columna de contraseña es opcional. Si no se proporciona, se generará una automáticamente.</p>
                                    <p class="instruction-step">4. Suba el archivo y en el siguiente paso podrá mapear sus columnas con los campos requeridos.</p>
                                </div>

                                @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                                @endif

                                <form action="{{route('dashboard.process.import')}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-4">
                                        <label for="file">Adjunta el archivo de excel:</label>
                                        <input type="file" class="form-control" name="file" id="file" required accept=".xlsx,.xls">
                                        @error('file')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-submit">
                                            <i class="fa-solid fa-upload"></i> Subir archivo
                                        </button>
                                        <a href="{{ route('dashboard.welcome') }}" class="btn btn-cancel">
                                            <i class="fa-solid fa-arrow-left"></i> Volver
                                        </a>
                                    </div>
                                </form>

                                <br><br>
                                <div class="text-center">
                                @if(Auth::user()->hasRole('administrator'))
                                    <a href="{{ route('dashboard.download.template.admin') }}" class="example-file">Descargar plantilla para administradores (todas las columnas)</a>
                                    <br>
                                    <a href="{{ route('dashboard.download.template.institution') }}" class="example-file mt-2">Descargar plantilla para instituciones (columnas simplificadas)</a>
                                @else
                                    <a href="{{ route('dashboard.download.template.institution') }}" class="example-file">Descargar plantilla de ejemplo para importación de usuarios</a>
                                @endif
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
@endpush
