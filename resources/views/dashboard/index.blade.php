@extends('layouts.main')

@section('title','Panel de administración')

@push('css')
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
</style>
@endpush

@section('content')

    <!--Crear nuevo usuario -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-nobackdrop">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="newModalLabel">Nueva usuario</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form" method="post" action="{{route('assessments.user.new')}}">
                    @csrf
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="lastname">Apellidos</label>
                                <input type="text" id="lastname" name="lastname" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="email">Correo electrónico</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="locale">Idioma preferido</label>
                                <select name="locale" id="locale" class="form-control" required>
                                    @foreach ($locales as $locale => $language)
                                        <option value="{{$locale}}">{{$language}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="gender">Género</label>
                                <select name="gender" id="gender" class="form-control" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="N">No binario</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="role">Rol</label>
                                <select name="role" id="role" class="form-control" required>
                                    @if(Auth::user()->hasRole('administrator'))
                                        <option value="administrator">Administrador</option>
                                        <option value="institution">Institutos</option>
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
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
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

                                {{ $dataTable->table() }}
                                
                                <div class="text-center">
                                    @hasrole(['administrator'])
                                        <div class="btn-group text-center" role="group">
                                            <a  href="{{route('dashboard.sync')}}" class="btn btn-warning">Sincronizar usuarios</a>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#newModal" class="btn btn-success">Añadir usuario</a>
                                        </div>
                                    @endhasrole
                                    @hasanyrole(['institution','coordinator'])
                                        <div class="btn-group text-center" role="group">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#newModal" class="btn btn-success">Añadir usuario</a>
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
                        @role('administrator')
                            <div class="col-xl-6">
                                <div class="box-inner text-center">
                                    <img src="{{asset('assets/img/doc.png')}}" class="mb-3">
                                    <a href="{{route('users.edit',auth()->user()->uuid)}}">
                                        <h4>Perfil</h4>
                                    </a>
                                    <p>Editar información</p>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="box-inner text-center">
                                    <img src="{{asset('assets/img/configuration.png')}}" class="mb-3">
                                    <a href="#">
                                        <h4>Ajustes</h4>
                                    </a>
                                    <p>Configuración</p>
                                </div>
                            </div>
                        @endrole
                        @hasanyrole(['coordinator','respondent'])
                            <div class="col-xl-12">
                                <div class="box-inner text-center">
                                    <img src="{{asset('assets/img/doc.png')}}" class="mb-3">
                                    <a href="{{route('users.edit',auth()->user()->uuid)}}">
                                        <h4>Perfil</h4>
                                    </a>
                                    <p>Editar información</p>
                                </div>
                            </div>
                        @endhasanyrole
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
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function(){
    });
</script>
@endpush