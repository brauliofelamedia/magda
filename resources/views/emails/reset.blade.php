<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido a {{env('APP_NAME')}}</title>
</head>
<body>
    <h1>Hola, {{ $name }}!</h1>
    <p>Se ha solicitado un cambio de contraseña a nuestra plataforma.</p>
    <p><strong>Te compartimos los nuevos datos de acceso:</strong></p>
    <p>
        <strong>Url de acceso:</strong> {{env('APP_URL')}}<br>
        <strong>Correo:</strong> {{$email}}<br>
        <strong>Contraseña:</strong> {{$password}}<br>
    </p>
    <p>¡Esperamos que disfrutes de nuestros servicios!</p>
    <p>Con cariño, <br>{{env('APP_NAME')}}.</p>
</body>
</html>
