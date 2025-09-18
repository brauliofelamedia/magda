@extends('emails.layouts.email-template')

@section('title', '¡Bienvenido a ' . config('app.name') . '!')

@section('header', '¡Bienvenido a ' . config('app.name') . '!')

@section('content')
    <div class="greeting">¡Hola, {{ $name }}!</div>
    
    <p>Gracias por unirte a nuestra plataforma. Hemos creado una cuenta para ti y te damos la bienvenida a nuestra comunidad.</p>
    
    <div class="info-block">
        <h3>Datos de acceso:</h3>
        <div class="credentials">
            <p><span class="highlight">URL de acceso:</span> {{ config('app.url') }}</p>
            @if($institution)
            <p><span class="highlight">Nombre de la institución:</span> {{$institution}}</p>
            @endif
            <p><span class="highlight">Correo:</span> {{$email}}</p>
            <p><span class="highlight">Contraseña:</span> {{$password}}</p>
        </div>
    </div>
    
    <p>Te recomendamos cambiar esta contraseña temporal por una de tu elección tan pronto como inicies sesión.</p>
    
    <a href="{{ config('app.url') }}" class="btn">Comenzar Ahora</a>
    
    <p>¡Esperamos que disfrutes de nuestros servicios y tengas una excelente experiencia!</p>
@endsection
