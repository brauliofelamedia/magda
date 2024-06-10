@extends('layouts.main')

@section('title','Registrar una cuenta')

@push('css')
@endpush

@section('content')
    <div class="container" id="login">
        <div class="row">
            <div class="col-12 mb-4">
                <img src="{{asset('assets/img/login.png')}}" alt="Login" class="c-auto img">
                <h3 class="text-center fw-700 c-blue">Registrar una cuenta</h3>
            </div>
            <div class="col-md-4 offset-md-4">
                <form action="{{route('register')}}" class="login" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" class="form-control  @error('name') is-invalid @enderror" placeholder="Nombre" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control  @error('email') is-invalid @enderror" placeholder="Correo electrónico" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password" id="password-confirm" class="form-control" placeholder="Contraseña" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <button type="submit" href="#" class="btn bg-orange text-center b-block c-white fw-600">Registrar cuenta</button>
                    <div class="row mt-2">
                        <div class="col-md-12 text-center">
                            <a href="#" class="t-none">Olvide la contraseña</a>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col">
                            <a href="{{route('login')}}" class="btn bg-transparent text-center b-block c-blue">Iniciar sesión</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush