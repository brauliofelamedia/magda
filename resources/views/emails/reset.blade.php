@extends('emails.layouts.email-template')

@section('title', 'Restablecimiento de Contraseña - ' . config('app.name'))

@section('header', 'Restablecimiento de Contraseña')

@section('content')
    <div class="greeting">¡Hola, {{ $name }}!</div>
    
    <p>Se ha solicitado un cambio de contraseña para tu cuenta en nuestra plataforma.</p>
    
    <div class="info-block">
        <h3>Nuevos datos de acceso:</h3>
        <div class="credentials">
            <p><span class="highlight">URL de acceso:</span> {{ config('app.url') }}</p>
            <p><span class="highlight">Correo:</span> {{$email}}</p>
            <p><span class="highlight">Contraseña:</span> {{$password}}</p>
        </div>
    </div>
    
    <p>Te recomendamos cambiar esta contraseña temporal por una de tu elección tan pronto como inicies sesión.</p>
    
    <a href="{{ config('app.url') }}" class="btn">Iniciar Sesión</a>
    
    <p>¡Esperamos que disfrutes de nuestros servicios!</p>
    
    <p>Si no solicitaste este cambio de contraseña, por favor contacta con nuestro equipo de soporte inmediatamente.</p>
@endsection
