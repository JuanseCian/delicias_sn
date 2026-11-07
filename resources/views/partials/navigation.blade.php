<header class="fixed inset-x-0 top-0 z-40 px-3 pt-3 sm:px-6 lg:px-8" x-data="{ open: false }">
    <nav class="mx-auto flex max-w-7xl items-center justify-between rounded-full border border-white/70 bg-crema/90 px-4 py-3 shadow-soft backdrop-blur-md sm:px-6" aria-label="Navegación principal">
        <a href="{{ route('home') }}" class="group flex items-center gap-2" aria-label="Volver al inicio de Delicias Sn">
            <img src="{{ asset('img/icono_delicias.png') }}" alt="" class="h-10 w-10 rounded-full object-cover transition-transform group-hover:rotate-6">
            <img src="{{ asset('img/delicias_nombre.png') }}" alt="Delicias Pastelería" class="h-10 w-36 object-contain object-left sm:w-40">
        </a>

        <div class="hidden items-center gap-7 text-xs font-semibold uppercase tracking-[0.16em] text-ink/70 lg:flex">
            <a href="#catalogo" class="transition-colors hover:text-bordo">Productos</a>
            <a href="#como-encargar" class="transition-colors hover:text-bordo">Cómo encargar</a>
            <a href="#personalizadas" class="transition-colors hover:text-bordo">Personalizadas</a>
            <a href="{{ route('faq') }}" class="transition-colors hover:text-bordo">Preguntas</a>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="cartOpen = true" class="relative grid h-11 w-11 place-items-center rounded-full bg-blush text-bordo transition hover:bg-rosa-viejo hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-bordo" aria-label="Abrir carrito">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                <span x-show="cartCount > 0" x-text="cartCount" x-cloak class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-bordo px-1 text-[10px] font-bold text-white"></span>
            </button>
            <button type="button" @click="open = !open" class="grid h-11 w-11 place-items-center rounded-full border border-bordo/15 text-bordo lg:hidden" :aria-expanded="open.toString()" aria-label="Abrir menú">
                <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </nav>
    <div x-show="open" x-transition x-cloak class="mx-auto mt-2 max-w-7xl rounded-3xl border border-white/70 bg-crema p-5 shadow-soft lg:hidden">
        <div class="grid gap-1 text-sm font-semibold uppercase tracking-[0.12em] text-ink/75">
            <a @click="open = false" href="#catalogo" class="rounded-2xl px-4 py-3 hover:bg-blush">Productos</a>
            <a @click="open = false" href="#como-encargar" class="rounded-2xl px-4 py-3 hover:bg-blush">Cómo encargar</a>
            <a @click="open = false" href="#personalizadas" class="rounded-2xl px-4 py-3 hover:bg-blush">Personalizadas</a>
            <a @click="open = false" href="{{ route('faq') }}" class="rounded-2xl px-4 py-3 hover:bg-blush">Preguntas frecuentes</a>
        </div>
    </div>
</header>
