<footer class="border-t {{ config('delicias.christmas_theme_enabled') ? 'border-oro/30' : 'border-bordo/10' }} bg-mist/70 px-4 pb-8 pt-14 text-ink sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-[1.5fr_repeat(3,1fr)] lg:gap-8">
            <div>
                <div class="flex items-center gap-3">
                    <x-brand-mark size="h-12 w-12" />
                    <img src="{{ asset('img/delicias_nombre.png') }}" alt="Delicias SN" class="brand-wordmark h-12 w-44 object-contain object-left">
                </div>
                <p class="mt-4 max-w-xs text-sm leading-6 text-ink/60">Pastelería artesanal hecha a mano para convertir cualquier día en una celebración.</p>
                <div class="mt-6 flex gap-2">
                    <a href="https://instagram.com/{{ ltrim(config('delicias.instagram'), '@') }}" target="_blank" rel="noopener" class="group grid h-10 w-10 place-items-center rounded-full border border-bordo/15 text-bordo transition hover:-translate-y-1 hover:bg-bordo hover:text-white" aria-label="Instagram">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="4"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".7" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="https://wa.me/{{ config('delicias.whatsapp') }}" target="_blank" rel="noopener" class="group grid h-10 w-10 place-items-center rounded-full border border-bordo/15 text-bordo transition hover:-translate-y-1 hover:bg-bordo hover:text-white" aria-label="WhatsApp">
                        <img src="{{ asset('img/wsp.png') }}" alt="" class="h-5 w-5 object-contain grayscale transition group-hover:grayscale-0">
                    </a>
                </div>
            </div>

            <div>
                <p class="text-base font-bold text-bordo">Comprar</p>
                <div class="mt-5 space-y-3 text-sm text-ink/60">
                    <a class="block transition hover:text-bordo" href="{{ route('catalog.index') }}">Productos</a>
                    <a class="block transition hover:text-bordo" href="{{ request()->routeIs('home') ? '#como-encargar' : route('home').'#como-encargar' }}">Cómo encargar</a>
                    <a class="block transition hover:text-bordo" href="{{ route('checkout') }}">Carrito y pedido</a>
                </div>
            </div>

            <div>
                <p class="text-base font-bold text-bordo">Conocenos</p>
                <div class="mt-5 space-y-3 text-sm text-ink/60">
                    <a class="block transition hover:text-bordo" href="{{ route('personalized') }}">Tortas personalizadas</a>
                    <a class="block transition hover:text-bordo" href="{{ route('reviews.public') }}">Opiniones</a>
                    <a class="block transition hover:text-bordo" href="{{ route('faq') }}">Preguntas frecuentes</a>
                </div>
            </div>

            <div>
                <p class="text-base font-bold text-bordo">Contacto</p>
                <div class="mt-5 space-y-3 text-sm leading-6 text-ink/60">
                    <a class="block transition hover:text-bordo" href="https://wa.me/{{ config('delicias.whatsapp') }}" target="_blank" rel="noopener">{{ config('delicias.whatsapp_display') }}</a>
                    <p>{{ config('delicias.hours') }}<br>Pedidos coordinados por WhatsApp.</p>
                    <a href="https://www.google.com/maps/search/?api=1&query=Jujuy+422" target="_blank" rel="noopener" class="inline-flex items-center gap-2 font-semibold text-bordo hover:text-ink">{{ config('delicias.address') }} <span>↗</span></a>
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col justify-between gap-3 border-t border-bordo/10 pt-6 text-xs text-ink/45 sm:flex-row">
            <p>© {{ date('Y') }} Delicias Sn · Desarrollado por <a href="https://developerscv.dev" target="_blank" rel="noopener" class="font-semibold text-bordo transition hover:text-ink">c&amp;vdev</a></p>
            <p>Hecho con tiempo y cariño.@if (config('delicias.christmas_theme_enabled')) <span class="text-bordo"> · ¡Felices fiestas!</span>@endif</p>
        </div>
    </div>
</footer>
