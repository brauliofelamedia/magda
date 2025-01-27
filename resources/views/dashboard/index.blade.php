@extends('layouts.main')

@section('title','Panel de administración')

@push('css')
<link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.css">
<style>
    .mt-10 {
        margin-top: 70px!important;
    }

    .box {
        padding-top: 230px!important;
    }

    .alga {
        top: 0 !important;
        bottom: 0 !important;
    }
    .coral {
        top: 0 !important;
        bottom: 0 !important;
    }
    .form-control {
        padding: 10px 23px!important;
    }

    .btn-disabled {
        background-color: #dfdfdf;
        color: black;
        cursor: not-allowed;
    }

    .btn-disabled:hover {
        background-color: #dfdfdf!important;
        color: black!important;
        cursor: not-allowed;
    }

    h5 {
        font-weight: bold;
        color: #f74219;
    }

    label {
        font-weight: bold;
        color: #033a60;
    }

    tr > td {
        text-align: left!important;
    }

    tr th::nth-child(2){
        display: none!important;
    }

    th span {
        color: white;
        font-weight: 500;
    }

    th {
        background: #f74219 !important;
    }

    .dt-paging-button.current {
        background-color: #ffc107 !important;
        color: white !important;
    }

    .first, .last {
        display: none !important;
    }
</style>
@endpush

@section('content')

    <!--Crear nuevo usuario -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-nobackdrop">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="newModalLabel">Nuevo usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form" method="post" action="{{route('assessments.user.new')}}">
                    @csrf
                    <div class="row" id="institution_name" style="display: none;">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="name">Nombre de la institución:</label>
                                <input type="text" id="name_institution" name="name_institution" class="form-control" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12" id="legal_representative" style="display: none;">
                            <h5>Representante legal:</h5>
                        </div>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="role">Rol:</label>
                                <select name="role" id="role" class="form-control" required>
                                    @if(Auth::user()->hasRole('administrator'))
                                        <option value="administrator">Administrador</option>
                                        <option value="institution">Institución</option>
                                    @endif
                                    @if(Auth::user()->hasRole('institution'))
                                        <option value="coordinator">Coordinador</option>
                                        <option value="respondent">Evaluado</option>
                                    @else
                                        <option value="respondent">Evaluado</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-12" id="institution">
                            <div class="form-group">
                                <label for="name">Selecciona una institución:</label>
                                <select name="user_id" class="form-control">
                                    @foreach($institutions as $institution)
                                        <option value="{{$institution->id}}">{{$institution->name_institution}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="name">Nombre:</label>
                                <input type="text" id="name" name="name" class="form-control" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="lastname">Apellidos:</label>
                                <input type="text" id="lastname" name="lastname" class="form-control" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="gender">Género:</label>
                                <select name="gender" id="gender" class="form-control" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="N">No binario</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="email">Correo electrónico:</label>
                                <input type="email" id="email" name="email" class="form-control" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="locale">Idioma preferido:</label>
                                <select name="locale" id="locale" class="form-control" required>
                                    @foreach ($locales as $locale => $language)
                                        <option value="{{$locale}}">{{$language}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="password">Contraseña:</label>
                                <input type="password" id="password" name="password" class="form-control" autocomplete="off">
                            </div>
                            <p>Si se deja en blanco, se generará una contraseña aleatoria.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Registrar usuario</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-10">
            <div class="col-12">
                @include('parts.message')
                <div class="box">
                    <img src="{{asset('assets/img/octopus.png')}}" alt="" class="octopus">
                    <img src="{{asset('assets/img/alga.png')}}" alt="" class="alga">
                    <img src="{{asset('assets/img/burbujas.png')}}" alt="" class="burbujas">
                    <img src="{{asset('assets/img/coral.png')}}" alt="" class="coral">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Usuarios</h3>

                                <table id="users-table" class="table table-striped responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Rol</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->fullname }}</td>
                                                <td>{{ $user->emailCut }}</td>
                                                <td>{{ $user->rol }}</td>
                                                <td>
                                                    <a href="{{route('users.edit',$user->uuid)}}" class="edit btn btn-blue btn-sm">Editar perfil</a>
                                                    @if(Auth::user()->hasRole(['administrator','institution','coordinator']))
                                                        @if(!empty($user->account_id))
                                                            <a href="{{route('assessments.index',$user->account_id)}}" class="btn btn-warning btn-sm">Evaluaciones</a>
                                                        @else
                                                            <a href="#" class="btn btn-disabled btn-sm" disabled>Evaluaciones</a>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="text-center">
                                    @hasrole(['administrator'])
                                        <div class="btn-group text-center" role="group">
                                            <a  href="{{route('dashboard.sync')}}" class="btn btn-warning"><i class="fas fa-sync"></i> Sincronizar usuarios</a>
                                        </div>
                                        <div class="btn-group text-center" role="group">
                                            <a  href="{{route('dashboard.import')}}" class="btn btn-info"><i class="fas fa-file-import"></i> Importar usuarios</a>
                                        </div>
                                    @endhasrole
                                    @hasanyrole(['administrator','institution'])
                                        <div class="btn-group text-center" role="group">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#newModal" class="btn btn-success"><i class="fas fa-users"></i> Añadir usuario</a>
                                        </div>
                                    @endhasanyrole
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="box-pink">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="box-inner">
                            <img src="{{asset('assets/img/logo-blue.png')}}" alt="{{env('APP_NAME')}}" class="text-center logo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
     new DataTable('#users-table', {
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });

    $(document).ready(function() {
        var institution = $('#institution');
        institution.hide();

        $('#role').change(function() {
            var role = $(this).val();
            var inputInstitution = $('#institution_name');

            var legalTitle = $('#legal_representative');

            if(role == 'institution'){
                inputInstitution.show()
                legalTitle.show();
                institution.hide();
            } else if(role == 'respondent'){
                legalTitle.hide();
                inputInstitution.hide()
                institution.show();
            } else {
                institution.show();
                legalTitle.hide();
                inputInstitution.hide()
            }
        });
    });
</script>
@endpush
