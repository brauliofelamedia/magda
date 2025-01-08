<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido a {{env('APP_NAME')}}</title>
</head>
<body>
    <h1>Hola, {{ $name }}!</h1>
    <p><strong>Te compartimos los datos para el acceso:</strong></p>
    <p>
        <strong>Url de acceso:</strong> {{env('APP_URL')}}<br>
        @if($institution)
        <strong>Nombre de la institución:</strong> {{$institution}}<br>
        @endif
        <strong>Correo:</strong> {{$email}}<br>
        <strong>Contraseña:</strong> {{$password}}
    </p>
    <p>¡Esperamos que disfrutes de nuestros servicios!</p>
    <p>Con cariño, <br>{{env('APP_NAME')}}.</p>
</body>
</html>
