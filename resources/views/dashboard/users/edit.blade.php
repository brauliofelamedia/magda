@extends('layouts.main')

@section('title','Editar usuario')

@push('css')
<style>
    label {
        font-weight: 700;
        font-size: 16px;
    }

    p {
        font-size: 16px!important;
    }
</style>
@endpush

@section('content')

    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-10">
            @include('parts.message')
            <div class="col-12">
                <div class="box">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Editar usuario</h3>
                                <div class="row">
                                    <div class="col-10 offset-1">
                                        <form action="{{route('users.update',$user->id)}}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">Nombre:</label>
                                                        <input type="text" id="name" name="name" class="form-control" value="{{$user->name}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="email">Correo electrónico:</label>
                                                        <input type="email" id="email" name="email" class="form-control" value="{{$user->email}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="user_id">Asignar a un instituto:</label>
                                                <select name="user_id" id="user_id" class="form-control">
                                                    <option value="">-- Selecciona a quien sera asignado el usuario --</option>
                                                    @foreach($institutes as $institute)
                                                        <option value="{{$institute->id}}" @if($institute->id == $user->user_id) selected @endif>{{$institute->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="role">Rol de usuario:</label>
                                                <select name="role" id="role" class="form-control">
                                                    <option value="">-- Selecciona el rol para el usuario --</option>
                                                    @role('administrator')
                                                    <option value="administrator">Administrator</option>
                                                    <option value="institution">Institución</option>
                                                    @endrole
                                                    @anyhasrole('institution','administrator')
                                                    <option value="coordinator">Coordinador</option>
                                                    <option value="respondent">Evaluado</option>
                                                    @endanyhasrole
                                                </select>
                                            </div>
                                            <hr>
                                            <h4>Cambiar contraseña</h4>
                                            <p>Si deseas cambiar la contraseña, necesitas rellenar la contraseña y confirmar la misma.</p>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Contraseña:</label>
                                                        <input type="password" name="password" class="form-control" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="">Repetir contraseña:</label>
                                                        <input type="password" name="password_confirmation" class="form-control" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-big btn-block">Guardar cambios</button><hr>
                                            <a href="{{route('dashboard.welcome')}}" class="text-center btn btn-big btn-danger btn-block">Cancelar y volver</a>
                                        </form>
                                    </div>
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
@endpush