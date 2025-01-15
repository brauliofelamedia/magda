<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido a {{env('APP_NAME')}}</title>
</head>
<body>
    <h1>Hola, {{ $name }}!</h1>
    <p><strong>Te han asignado una nueva evaluación:</strong></p>
    <p>
        <strong>Url de evaluación:</strong>{{$url}}<br>
    </p>
    <p>¡Esperamos que disfrutes de nuestros servicios!</p>
    <p>Con cariño, <br>{{env('APP_NAME')}}.</p>
</body>
</html>
