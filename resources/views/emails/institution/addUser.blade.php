<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido a {{env('APP_NAME')}}</title>
</head>
<body style="margin: 0; padding: 20px 0; background-color: #f7f7f7; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 30px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); padding: 20px;">
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
    </div>
</body>
</html>
