<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gestión privada · Delicias Sn')</title>
    @vite(['resources/css/app.css', 'resources/js/landing.ts'])
</head>
<body class="private-shell min-h-screen antialiased">
    @yield('content')
    <x-private-toast />
</body>
</html>
