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
            <div class="col-12">
                <div class="box">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Editar usuario</h3>
                                <div class="row">
                                    <div class="col-6 offset-3">
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                        <form action="{{route('users.update',$user->id)}}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-group">
                                                <label for="">Nombre:</label>
                                                <input type="text" name="name" class="form-control" value="{{$user->name}}">
                                            </div>
                                        
                                            <div class="form-group">
                                                <label for="">Correo electrónico:</label>
                                                <input type="email" name="email" class="form-control" value="{{$user->email}}">
                                            </div>

                                            <hr>
                                            <h4>Cambiar contraseña</h4>
                                            <p>Si deseas cambiar la contraseña, necesitas rellenar la contraseña y confirmar la misma.</p>
                                            <div class="form-group">
                                                <label for="">Contraseña:</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label for="">Repetir contraseña:</label>
                                                <input type="password" name="password_confirmation" class="form-control">
                                            </div>

                                            <button type="submit" class="btn btn-primary btn-big btn-block">Guardar cambios</button><hr>
                                            <a href="{{route('dashboard.welcome')}}" class="btn btn-big btn-danger btn-block">Cancelar y volver</a>
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