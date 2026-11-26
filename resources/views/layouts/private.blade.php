<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>@yield('title', 'Gestión privada · Delicias Sn')</title>
    <link rel="icon" href="{{ asset('img/icono_delicias.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/icono_delicias.png') }}">
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/landing.ts'])
</head>
<body class="private-shell min-h-screen antialiased">
    <nav class="border-b border-[#263238]/10 bg-white px-4 py-3 sm:px-6 lg:px-10" aria-label="Navegación privada">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-2 text-xs font-bold text-[#64747A]">
            <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 hover:bg-[#F5F1EC] hover:text-[#6B2D3C]">Panel</a>
            <a href="{{ route('private.orders.index') }}" class="rounded-lg px-3 py-2 hover:bg-[#F5F1EC] hover:text-[#6B2D3C]">Pedidos</a>
            <a href="{{ route('private.products.index') }}" class="rounded-lg px-3 py-2 hover:bg-[#F5F1EC] hover:text-[#6B2D3C]">Productos</a>
            <a href="{{ route('private.reviews.index') }}" class="rounded-lg px-3 py-2 hover:bg-[#F5F1EC] hover:text-[#6B2D3C]">Valoraciones</a>
            <a href="{{ route('private.whatsapp.index') }}" class="rounded-lg bg-[#EAF7EE] px-3 py-2 text-[#1B7F45] hover:bg-[#D9F0E1]">WhatsApp</a>
        </div>
    </nav>
    @yield('content')
    <x-private-toast />
</body>
</html>
