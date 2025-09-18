@extends('emails.layouts.email-template')

@section('title', 'Correo de Prueba - ' . config('app.name'))

@section('header', 'Correo de Prueba')

@section('content')
    <div class="greeting">¡Hola!</div>
    
    <p>Este es un correo de prueba enviado desde la plataforma {{ config('app.name') }}.</p>
    
    <div class="info-block">
        <h3>Información de la prueba:</h3>
        <p><span class="highlight">Fecha y hora:</span> {{ date('Y-m-d H:i:s') }}</p>
        <p><span class="highlight">Entorno:</span> {{ app()->environment() }}</p>
        <p><span class="highlight">URL de la aplicación:</span> {{ config('app.url') }}</p>
    </div>
    
    <p>Si has recibido este correo, significa que el sistema de envío de correos electrónicos está funcionando correctamente.</p>
    
    <a href="{{ config('app.url') }}" class="btn">Visitar Plataforma</a>
@endsection