<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gestión privada · Delicias Sn')</title>
    <link rel="icon" href="{{ asset('img/icono_delicias.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/icono_delicias.png') }}">
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/landing.ts'])
</head>
<body class="private-shell min-h-screen antialiased" x-data="{ adminSidebarOpen: true, adminMobileOpen: false }">
    <div class="admin-dashboard-shell relative flex min-h-screen w-full overflow-hidden">
        <div class="pointer-events-none absolute -right-24 -top-24 z-0 size-72 rounded-full bg-blush/75 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-24 z-0 size-80 rounded-full bg-rosa-viejo/15 blur-3xl"></div>
        <div x-show="adminMobileOpen" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-ink/35 backdrop-blur-sm md:hidden" @click="adminMobileOpen = false"></div>

        <aside class="admin-sidebar fixed inset-y-0 left-0 z-50 hidden w-64 flex-col border-r border-bordo/10 bg-white/65 p-2 shadow-soft backdrop-blur-xl md:flex">
            <a href="{{ route('dashboard') }}" class="group flex h-14 items-center gap-2 rounded-2xl px-3 transition hover:bg-blush/70" aria-label="Ir al panel de gestión">
                <img src="{{ asset('img/icono_delicias.png') }}" alt="" class="h-9 w-9 rounded-xl object-cover shadow-sm transition-transform duration-300 group-hover:rotate-6">
                <img src="{{ asset('img/delicias_nombre.png') }}" alt="Delicias Pastelería" class="brand-wordmark ml-1 h-10 min-w-0 flex-1 object-contain object-left">
            </a>

            <div class="mt-3 rounded-2xl border border-bordo/10 bg-white/65 p-3 shadow-[0_8px_24px_rgba(107,45,60,0.06)]"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-rosa-viejo">Tu espacio</p><p class="mt-1 text-sm font-semibold text-ink">Catálogo de Delicias</p><p class="mt-1 text-xs leading-4 text-ink/55">Todo listo para mostrar tus productos.</p><a href="{{ route('private.products.create') }}" class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-bordo px-3 py-2 text-xs font-bold text-white transition hover:bg-ink">+ Nuevo producto</a></div>

            <nav class="mt-3 space-y-1.5 px-1" aria-label="Navegación del panel">
                <a href="{{ route('private.products.index') }}" class="admin-nav-link {{ request()->routeIs('private.products.*') ? 'is-active' : '' }}"><span class="admin-nav-icon">□</span><span>Productos</span></a>
                <a href="{{ route('private.orders.index') }}" class="admin-nav-link {{ request()->routeIs('private.orders.*') ? 'is-active' : '' }}"><span class="admin-nav-icon">▤</span><span>Pedidos</span></a>
            </nav>

            <nav class="mt-1 space-y-1.5 px-1" aria-label="Más opciones del panel">
                <a href="{{ route('private.reviews.index') }}" class="admin-nav-link {{ request()->routeIs('private.reviews.*') ? 'is-active' : '' }}"><span class="admin-nav-icon">☆</span><span>Valoraciones</span></a>
            </nav>

            <div class="mt-auto space-y-3 px-1">
                <a href="{{ route('home') }}" target="_blank" rel="noreferrer" class="admin-public-link"><span>Ver página pública</span><span aria-hidden="true">↗</span></a>
                <div class="rounded-2xl border border-bordo/10 bg-crema/70 p-3"><p class="text-xs font-bold text-bordo">{{ auth()->user()->name }}</p><p class="mt-1 truncate text-[11px] text-ink/50">{{ auth()->user()->email }}</p></div>
            </div>
        </aside>

        <div class="admin-mobile-bar md:hidden">
            <a href="{{ route('dashboard') }}" class="group flex items-center gap-2" aria-label="Ir al panel de gestión"><img src="{{ asset('img/icono_delicias.png') }}" alt="" class="h-9 w-9 rounded-xl object-cover shadow-sm"><img src="{{ asset('img/delicias_nombre.png') }}" alt="Delicias Pastelería" class="brand-wordmark h-10 w-32 object-contain object-left"></a>
            <button type="button" @click="adminMobileOpen = true" class="admin-menu-button" aria-label="Abrir navegación del panel"><span></span><span></span><span></span></button>
        </div>

        <aside x-show="adminMobileOpen" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="admin-sidebar fixed inset-y-0 left-0 z-50 flex w-[18rem] flex-col border-r border-bordo/10 bg-white px-3 py-3 shadow-soft md:hidden">
            <div class="flex h-14 items-center justify-between"><a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-display text-xl text-bordo"><img src="{{ asset('img/icono_delicias.png') }}" alt="" class="h-9 w-9 rounded-xl object-cover">Delicias</a><button type="button" @click="adminMobileOpen = false" class="admin-menu-button" aria-label="Cerrar navegación"><span class="rotate-45"></span><span class="-rotate-45"></span></button></div>
            <nav class="mt-4 space-y-1.5 px-1" aria-label="Navegación del panel móvil">
                <a @click="adminMobileOpen = false" href="{{ route('private.products.index') }}" class="admin-nav-link {{ request()->routeIs('private.products.*') ? 'is-active' : '' }}"><span class="admin-nav-icon">□</span><span>Productos</span></a>
                <a @click="adminMobileOpen = false" href="{{ route('private.orders.index') }}" class="admin-nav-link {{ request()->routeIs('private.orders.*') ? 'is-active' : '' }}"><span class="admin-nav-icon">▤</span><span>Pedidos</span></a>
                <a @click="adminMobileOpen = false" href="{{ route('private.reviews.index') }}" class="admin-nav-link {{ request()->routeIs('private.reviews.*') ? 'is-active' : '' }}"><span class="admin-nav-icon">☆</span><span>Valoraciones</span></a>
            </nav>
            <a href="{{ route('home') }}" target="_blank" rel="noreferrer" class="admin-public-link mt-auto"><span>Ver página pública</span><span aria-hidden="true">↗</span></a>
        </aside>

        <div class="admin-content relative z-10 min-h-screen md:pl-64">
            @yield('content')
        </div>
    </div>
    <x-private-toast />
</body>
</html>
