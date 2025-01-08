@extends('layouts.main')

@section('title','Recuperar contraseña')

@push('css')
@endpush

@section('content')
    <div class="container" id="login">
        <div class="row">
            <div class="col-12 mb-4">
                <img src="{{asset('assets/img/forgot.png')}}" alt="Login" class="c-auto img">
                <h2 class="text-center fw-700 c-blue">Reiniciar contraseña</h2>
            </div>
            <div class="col-md-4 offset-md-4">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <form action="{{ route('users.password.reset') }}" class="login" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Correo electrónico" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button type="submit" class="btn bg-orange text-center b-block c-white fw-600">Solicitar contraseña</button>
                    <div class="row mt-6">
                        <div class="col text-center">
                            <a href="{{route('login')}}">Iniciar sesión</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
