@php
    $christmasThemeEnabled = config('delicias.christmas_theme_enabled');
    $seoTitle = trim($__env->yieldContent('title', 'Delicias SN | Pastelería artesanal en San Nicolás'));
    $seoDescription = trim($__env->yieldContent('description', 'Delicias SN es pastelería artesanal en San Nicolás de los Arroyos. Conocé tortas, cupcakes, cookies, boxes y mesas dulces para tus celebraciones.'));
    $seoCanonical = trim($__env->yieldContent('canonical', delicias_canonical_url()));
    $seoRobots = trim($__env->yieldContent('robots', 'index, follow'));
    $seoOgType = trim($__env->yieldContent('og_type', 'website'));
    $seoOgImage = trim($__env->yieldContent('og_image', delicias_canonical_url('img/icono_delicias.png')));
    $seoOgImageAlt = trim($__env->yieldContent('og_image_alt', 'Delicias SN, pastelería artesanal'));
@endphp

<!doctype html>
<html lang="es" class="{{ $christmasThemeEnabled ? 'theme-christmas' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $seoRobots }}">
    <meta name="author" content="Delicias SN">
    <meta name="theme-color" content="#6b2d3c">
    <link rel="canonical" href="{{ $seoCanonical }}">
    <meta property="og:locale" content="es_AR">
    <meta property="og:type" content="{{ $seoOgType }}">
    <meta property="og:site_name" content="Delicias SN">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:image" content="{{ $seoOgImage }}">
    <meta property="og:image:alt" content="{{ $seoOgImageAlt }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoOgImage }}">
    <title>{{ $seoTitle }}</title>
    <link rel="icon" href="{{ asset('img/icono_delicias.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/icono_delicias.png') }}">
    @vite(['resources/css/app.css', 'resources/js/landing.ts'])
    @if (request()->routeIs('personalized'))
        @vite('resources/js/cake-designer-entry.ts')
    @endif
    @include('partials.analytics')
    @include('partials.structured-data', ['schemas' => [
        [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => delicias_canonical_url().'#organization',
            'name' => 'Delicias SN',
            'url' => delicias_canonical_url(),
            'logo' => delicias_canonical_url('img/icono_delicias.png'),
            'sameAs' => ['https://instagram.com/'.ltrim(config('delicias.instagram'), '@')],
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => delicias_canonical_url().'#website',
            'name' => 'Delicias SN',
            'url' => delicias_canonical_url(),
            'inLanguage' => 'es-AR',
            'publisher' => ['@id' => delicias_canonical_url().'#organization'],
        ],
    ]])
    @stack('head')
    @stack('structured-data')
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
        @if ($christmasThemeEnabled)
            <x-christmas-decorations :snow="config('delicias.christmas_snow_enabled')" />
        @endif
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
