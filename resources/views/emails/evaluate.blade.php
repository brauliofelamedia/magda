@extends('emails.layouts.email-template')

@section('title', 'Nueva Evaluación Asignada - ' . config('app.name'))

@section('header', 'Nueva Evaluación Asignada')

@section('content')
    <div class="greeting">¡Hola, {{ $name }}!</div>
    
    <p>Te informamos que se te ha asignado una nueva evaluación en nuestra plataforma.</p>
    
    <div class="info-block">
        <h3>Detalles de la evaluación:</h3>
        <p>Para acceder a tu evaluación asignada, haz clic en el botón de abajo o utiliza el siguiente enlace:</p>
    </div>
    
    <a href="{{$url}}" class="btn">Acceder a la Evaluación</a>
    
    <p style="margin-top: 20px;">Si el botón no funciona, puedes copiar y pegar la siguiente URL en tu navegador:</p>
    <p style="word-break: break-all; font-size: 14px; color: #666;">{{ $url }}</p>
    
    <p style="margin-top: 20px;">Por favor, completa esta evaluación lo antes posible para garantizar el proceso adecuado.</p>
    
    <p>¡Gracias por tu colaboración!</p>
@endsection
