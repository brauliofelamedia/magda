@extends('layouts.main')

@section('title','Registrar una cuenta')

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
                <form action="{{ route('login') }}" class="login" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Correo electrónico" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña" required autocomplete="current-password" >
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button type="submit" href="#" class="btn bg-orange text-center b-block c-white fw-600">Iniciar sesión</button>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>
                        </div>
                        @if (Route::has('password.request'))
                            <div class="col-md-6">
                                <a href="{{ route('password.request') }}" class="f-right t-none">Olvide la contraseña</a>
                            </div>
                        @endif
                        
                    </div>
                    <div class="row mt-5">
                        <div class="col">
                            <a href="{{route('register')}}" class="btn bg-transparent text-center b-block c-blue">Registrar una cuenta</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush