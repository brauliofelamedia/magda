<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido a {{env('APP_NAME')}}</title>
</head>
<body>
    <h1>¡Bienvenido, {{ $user->name }}!</h1>
    <p>Gracias por registrarte en nuestra aplicación.</p>
    <p><strong>Te compartimos los datos para el acceso:</strong></p>
    <p>
        <strong>Url de acceso:</strong> {{env('APP_URL')}}<br>
        <strong>Correo:</strong> {{$user->email}}<br>
        <strong>Contraseña:</strong> {{$user->email}}<br>
    </p>
    <p>¡Esperamos que disfrutes de nuestros servicios!</p>
    <p>Con cariño, <br>{{env('APP_NAME')}}.</p>
</body>
</html>