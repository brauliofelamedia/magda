@extends('layouts.main')

@section('title','Editar usuario')

@push('css')
<style>
    label {
        font-weight: 700;
        font-size: 16px;
    }

    .btn-invitate {
        background-color: #023862;
        color: white;
        margin: 15px 0;
    }

    .btn-invitate:hover {
        color: white;
        background-color: #02487e;
    }

    p {
        font-size: 16px!important;
    }

    .box {
        padding-top: 230px!important;
    }

    .avatar-preview {
        height: 130px;
        width: 130px;
        border-radius: 50%;
        margin-bottom: 20px;
        border: 4px solid #0DC863;
        background-size: cover;
        background-position: center;
    }

    .octopuss {
        position: absolute;
        top: -150px;
        margin: 0 auto;
        width: 750px;
        text-align: center;
        display: block;
        left: 50%;
        transform: translate(-50%, 0);
    }

    /*Móviles*/
    @media (max-width: 575px) {
        .box {
            padding-top: 100px !important;
        }

        .octopuss {
            width: 80%;
            top: -40px;
        }
    }

    .type {
        padding: 20px;
        background-color: #ececec;
        border-radius: 10px;
    }

    h5 {
        color: red;
        font-weight: bold;
    }

    /*Tablets*/
    @media (min-width: 768px) {
    }

    /*Laptops*/
    @media (min-width: 992px) {
    }

    /*Desktop*/
    @media (min-width: 1200px) {
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')

    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-5">
            @include('parts.message')
            <div class="col-12">
                <div class="box">
                    <img src="{{asset('assets/img/edit.png')}}" alt="Octopus" class="img-fluid octopuss">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Editar usuario</h3>
                                <div class="row">
                                    <div class="col-12">
                                        <form action="{{route('users.update',$user->uuid)}}" autocomplete="off" autoComplete='off' method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="avatar-preview" style="background-image: url('{{ $user->avatar_url }}')"></div>
                                                        <input type="file" name="avatar">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @if($user->hasRole('institution'))
                                                    <div class="row" id="institution_name">
                                                        <div class="col-xl-12">
                                                            <div class="form-group">
                                                                <label for="name">Nombre de la institución:</label>
                                                                <input type="text" id="name_institution" name="name_institution" class="form-control" autocomplete="false" value="{{$user->name_institution}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12" id="legal_representative">
                                                        <h5>Representante legal:</h5>
                                                    </div>
                                                @endif
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="name">Nombre:</label>
                                                        <input type="text" id="name" name="name" class="form-control" value="{{$user->name}}" autocomplete="false">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="last_name">Apellidos:</label>
                                                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{$user->last_name}}" autocomplete="false">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="email">Correo electrónico:</label>
                                                        <input type="email" id="email" name="email" class="form-control" value="{{$user->email}}" @if(Auth()->user()->hasRole('respondent')) readonly @endif>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(!$user->hasRole('institution'))
                                                @hasanyrole('administrator|coordinator')
                                                    <div class="form-group">
                                                        <label for="user_id">Asignar a un instituto:</label>
                                                        <select name="user_id" id="user_id" class="form-control">
                                                            <option value="">-- Selecciona a quien sera asignado el usuario --</option>
                                                            @foreach($institutes as $institute)
                                                                <option value="{{$institute->id}}" @if($institute->id == $user->user_id) selected @endif>{{$institute->name_institution}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endhasanyrole
                                            @endif
                                            @hasanyrole('institution|administrator|coordinator')
                                                <div class="form-group">
                                                    <label for="role">Rol de usuario:</label>
                                                    <select name="role" id="role" class="form-control" @if(Auth()->user()->id == $user->id) readonly @endif>
                                                        @role('administrator')
                                                            <option value="">-- Selecciona el rol para el usuario --</option>
                                                            <option value="administrator" @if($user->hasRole('administrator')) selected @endif>Administrator</option>
                                                            <option value="coordinator" @if($user->hasRole('coordinator')) selected @endif>Coordinador</option>
                                                            <option value="institution" @if($user->hasRole('institution')) selected @endif>Institución</option>
                                                            <option value="respondent" @if($user->id && $user->hasRole('respondent')) selected @endif>Evaluado</option>
                                                        @endrole
                                                        @role('institution')
                                                            <option value="institution" @if(Auth()->user()->hasRole('institution')) selected @endif>Institución</option>
                                                        @endrole
                                                        @role('respondent')
                                                            <option value="respondent" @if(Auth()->user()->hasRole('respondent')) selected @endif>Evaluado</option>
                                                        @endrole
                                                    </select>
                                                </div>
                                            @endhasanyrole
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="type">
                                                        <label for="type_of_evaluation">Tipo de evaluación: <small class="form-text text-muted">Selecciona uno o más tipos de evaluación.</small></label>
                                                        <div class="form-group" style="margin-bottom: 0;">
                                                            <div class="form-check">
                                                                <input type="checkbox" id="evaluation_short" name="type_of_evaluation[]" value="short" class="form-check-input"
                                                                    @if(is_array($user->type_of_evaluation) && in_array('short', $user->type_of_evaluation)) checked @endif>
                                                                <label class="form-check-label" for="evaluation_short">Evaluación corta (intereses) - 60 preguntas</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="checkbox" id="evaluation_long" name="type_of_evaluation[]" value="long" class="form-check-input"
                                                                    @if(is_array($user->type_of_evaluation) && in_array('long', $user->type_of_evaluation)) checked @endif>
                                                                <label class="form-check-label" for="evaluation_long">Evaluación larga (comportamientos, intereses y cognitivo) - 174 preguntas</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-big btn-block" style="background: #0DC863; border: 0; margin-bottom: 5px; font-size: 18px; padding: 15px; margin-top:15px;">Guardar cambios</button>
                                        </form>
                                        @hasrole('administrator')
                                            <form action="{{route('users.password.reset')}}" id="send-email-form" method="post">
                                                @csrf
                                                <input type="hidden" name="email" value="{{$user->email}}">
                                                <input type="hidden" name="type" value="reset_admin">
                                                <a class="btn btn-xs btn-invitate send-email"><i class="fas fa-envelope"></i> Reenviar correo de invitación</a>
                                                <button type="submit" style="display: none;"></button>
                                            </form>
                                        @endhasrole
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="box-inner" style="margin-top: 25px;">
                        <form action="{{route('users.update',$user->uuid)}}" method="post" >
                            @csrf
                            @method('PATCH')
                            <h4>Cambiar contraseña</h4>
                            <p>Si deseas cambiar la contraseña, necesitas rellenar la contraseña y confirmar la misma.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Contraseña:</label>
                                        <input type="password" name="password" class="form-control" autocomplete="false">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Repetir contraseña:</label>
                                        <input type="password" name="password_confirmation" class="form-control" autocomplete="false">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-big btn-block" style="background: #0DC863; border: 0; margin-bottom: 5px; font-size: 18px; padding: 15px;">Guardar cambios</button>                                            
                        </form>
                    </div>
                    <div class="row">
                        <div class="col">
                            <a href="{{route('dashboard.welcome')}}" class="text-center btn btn-big btn-danger btn-block" style="margin-top: 15px; font-size: 18px; padding: 15px;">Cancelar y regresar</a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
     $('#type_of_evaluation').select2({
        placeholder: "Selecciona el tipo de evaluación",
        allowClear: true
    });

    $(document).ready(function(){
        $('.send-email').on('click', function(){
            Swal.fire({
                title: "¿Estas seguro?",
                text: "Se enviará un correo de acceso al usuario y se reiniciará la contraseña.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "¡Si, estoy seguro!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#send-email-form').submit();
                    Swal.fire({
                        title: "Enviando",
                        text: "Espera un momento...",
                        icon: "warning",
                        showConfirmButton: false,
                    });
                }
            });
        });
    });
</script>
@endpush
