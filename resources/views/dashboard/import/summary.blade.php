@extends('layouts.main')

@section('title','Resumen de importación')

@push('css')
<style>
    .btn-submit {
        padding: 15px 25px !important;
        font-size: 16px !important;
        font-weight: bold !important;
        background-color: #033a60 !important;
        border: 0 !important;
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

    .btn-submit:hover {
        background-color: #035f9f !important;
    }
    
    .summary-section {
        background-color: #f8f9fa;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .stats-card {
        margin-bottom: 15px;
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
                                <h3 class="text-center">Resumen de importación de usuarios</h3>
                                
                                <div class="summary-section">
                                    <h5 class="mb-3">Estadísticas</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white stats-card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Usuarios importados</h5>
                                                    <h3 class="mb-0">{{ $summary['stats']['imported'] }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning text-white stats-card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Usuarios existentes</h5>
                                                    <h3 class="mb-0">{{ $summary['stats']['existing'] }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-danger text-white stats-card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Usuarios fallidos</h5>
                                                    <h3 class="mb-0">{{ $summary['stats']['failed'] }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white stats-card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Total procesados</h5>
                                                    <h3 class="mb-0">{{ $summary['stats']['total'] }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(count($summary['imported']) > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card summary-section">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Usuarios importados exitosamente</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Fila</th>
                                                        <th>Nombre</th>
                                                        <th>Correo</th>
                                                        <th>Contraseña</th>
                                                        <th>Estado del correo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($summary['imported'] as $user)
                                                    <tr>
                                                        <td>{{ $user['row'] }}</td>
                                                        <td>{{ $user['name'] }}</td>
                                                        <td>{{ $user['email'] }}</td>
                                                        <td>{{ $user['password'] }}</td>
                                                        <td><span class="badge bg-success">Enviado</span></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(count($summary['existing']) > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card summary-section">
                                    <div class="card-header bg-warning text-white">
                                        <h5 class="mb-0">Usuarios ya existentes (no importados)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Fila</th>
                                                        <th>Nombre</th>
                                                        <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($summary['existing'] as $user)
                                                    <tr>
                                                        <td>{{ $user['row'] }}</td>
                                                        <td>{{ $user['name'] }}</td>
                                                        <td>{{ $user['email'] }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(count($summary['failed']) > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card summary-section">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0">Usuarios con errores (no importados)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Fila</th>
                                                        <th>Nombre</th>
                                                        <th>Correo</th>
                                                        <th>Error</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($summary['failed'] as $user)
                                                    <tr>
                                                        <td>{{ $user['row'] }}</td>
                                                        <td>{{ $user['name'] }}</td>
                                                        <td>{{ $user['email'] }}</td>
                                                        <td>{{ $user['error'] }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('dashboard.import') }}" class="btn btn-cancel">
                                        <i class="fa-solid fa-arrow-left"></i> Volver a importar
                                    </a>
                                    <a href="{{ route('dashboard.welcome') }}" class="btn btn-primary btn-submit">
                                        <i class="fa-solid fa-home"></i> Ir al dashboard
                                    </a>
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