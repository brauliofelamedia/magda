<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        /* Estilos base */
        body {
            margin: 0;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background-color: #f7f7f7;
        }
        
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        /* Cabecera */
        .email-header {
            background: #f74219;
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .logo {
            max-width: 180px;
            margin-bottom: 10px;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        /* Contenido */
        .email-content {
            padding: 30px;
            line-height: 1.6;
        }
        
        .greeting {
            font-size: 20px;
            margin-bottom: 15px;
            color: #f74219;
        }
        
        /* Bloques de información */
        .info-block {
            background-color: #f8f9fa;
            border-left: 4px solid #f74219;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        /* Botones */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #f74219;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            margin: 15px 0;
            text-align: center;
        }
        
        /* Pie de página */
        .email-footer {
            background-color: #f0f2f5;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        
        .social-links {
            margin: 15px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #f74219;
        }
        
        /* Elementos específicos */
        .highlight {
            font-weight: 600;
            color: #f74219;
        }
        
        .credentials {
            margin: 15px 0;
        }
        
        .credentials p {
            margin: 5px 0;
        }
        
        /* Responsivo */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100%;
                border-radius: 0;
            }
            
            .email-header,
            .email-content,
            .email-footer {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>@yield('header', config('app.name'))</h1>
        </div>
        
        <div class="email-content">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p>© {{ date('Y') }} {{ config('app.name') }} - Todos los derechos reservados</p>
            <p>Si tienes alguna pregunta, no dudes en contactarnos</p>
        </div>
    </div>
</body>
</html>