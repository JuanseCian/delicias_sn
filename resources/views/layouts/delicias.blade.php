<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Delicias Sn · Pastelería artesanal hecha a mano en Catamarca 523.">
    <title>@yield('title', 'Delicias Sn · Pastelería artesanal')</title>
    <link rel="icon" href="{{ asset('img/icono_delicias.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/icono_delicias.png') }}">
    @vite(['resources/css/app.css', 'resources/js/landing.ts', 'resources/js/cake-designer-entry.ts'])
    @stack('head')
</head>
<body x-data="deliciasApp(@js($catalog ?? []), @js(config('delicias.whatsapp')), @js([
    'customer_name' => old('customer_name', ''),
    'phone' => old('phone', ''),
    'delivery_type' => old('delivery_type', 'envio'),
    'address' => old('address', ''),
    'delivery_date' => old('delivery_date', ''),
    'time_slot' => old('time_slot', '13 a 15 h'),
    'notes' => old('notes', ''),
]), @js(delicias_time_slots()))" x-init="init()" class="bg-crema text-ink antialiased">
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
