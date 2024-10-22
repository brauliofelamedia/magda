<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <title>@yield('title') - {{env('APP_NAME')}}</title>
  @stack('css')
  @vite(['resources/sass/main.scss','resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>