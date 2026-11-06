<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Delicias Sn · Pastelería artesanal hecha a mano en Jujuy 422.">
    <title>@yield('title', 'Delicias Sn · Pastelería artesanal')</title>
    @vite(['resources/css/app.css', 'resources/js/landing.ts'])
    @stack('head')
</head>
<body x-data="deliciasApp(@js($catalog ?? []), @js(config('delicias.whatsapp')))" x-init="init()" class="bg-crema text-ink antialiased">
    <div class="min-h-screen overflow-x-hidden">
        @include('partials.navigation')

        <main>
            @yield('content')
        </main>

        <x-whatsapp-button />
        <x-cart-drawer />
    </div>

    @stack('scripts')
</body>
</html>
