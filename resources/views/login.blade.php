@extends('layouts.main')

@section('title','Iniciar sesión')

@push('css')
@endpush

@section('content')
    <div class="container" id="login">
        <div class="row">
            <div class="col-12 mb-4">
                <img src="{{asset('assets/img/login.png')}}" alt="Login" class="c-auto img">
                <h1 class="text-center fw-700 c-blue">Bienvenido</h1>
            </div>
            <div class="col-md-4 offset-md-4">
                <form action="" class="login">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Usuario">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Contraseña">
                    </div>
                    <button type="submit" href="#" class="btn bg-orange text-center b-block c-white fw-600">Iniciar sesión</button>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="">
                                <input type="checkbox"> Recordarme
                            </label>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="f-right t-none">Olvide la contraseña</a>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col">
                            <button class="btn bg-transparent text-center b-block c-blue">Iniciar sesión</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush