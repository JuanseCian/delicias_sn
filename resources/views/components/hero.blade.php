<section class="hero-section relative grid min-h-[min(52rem,100dvh)] place-items-center overflow-hidden px-4 pb-16 pt-36 sm:px-6 sm:pb-24 sm:pt-44 lg:px-8 lg:pb-28 lg:pt-48">
    <div class="hero-wash hero-wash-left" aria-hidden="true"></div>
    <div class="hero-wash hero-wash-right" aria-hidden="true"></div>
    <div class="hero-grain" aria-hidden="true"></div>
    <div class="hero-intro relative z-10 mx-auto flex max-w-4xl flex-col items-center text-center reveal">
        <p class="eyebrow hero-enter" style="--hero-enter-delay: 80ms">{{ config('delicias.christmas_theme_enabled') ? 'Edición navideña · Pastelería artesanal' : 'Pastelería artesanal' }}</p>
        <h1 class="hero-enter mt-7 max-w-3xl font-display text-5xl leading-[0.92] tracking-[-0.055em] text-bordo sm:text-7xl lg:text-[7rem]" style="--hero-enter-delay: 180ms">Endulza<br><span class="font-script text-[1.08em] font-normal tracking-normal text-rosa-viejo">{{ config('delicias.christmas_theme_enabled') ? 'tus fiestas' : 'tus momentos' }}</span></h1>
        <p class="hero-enter mt-8 max-w-xl text-base leading-7 text-ink/65 sm:text-lg" style="--hero-enter-delay: 300ms">Tortas, cupcakes y boxes hechos por encargo para cumpleaños, meriendas y días que merecen algo dulce.</p>
        <div class="hero-enter mt-9 flex flex-col justify-center gap-3 sm:flex-row" style="--hero-enter-delay: 430ms">
            <a href="#catalogo" class="btn-primary">Elegir algo rico <span aria-hidden="true">↓</span></a>
            <a href="https://wa.me/{{ config('delicias.whatsapp') }}?text={{ rawurlencode('¡Hola! Quiero encargar algo rico en Delicias Sn.') }}" target="_blank" rel="noopener" class="btn-secondary">Pedir por WhatsApp <span aria-hidden="true">↗</span></a>
        </div>
        <div class="hero-enter mt-12 flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-xs font-semibold text-ink/45" style="--hero-enter-delay: 560ms"><span class="h-px w-10 bg-rosa-viejo"></span><span>Retiro en Jujuy 422</span><span aria-hidden="true">·</span><span>Envíos coordinados</span></div>
    </div>
    <div class="hero-scroll-hint" aria-hidden="true"><span></span> descubrí algo rico</div>
</section>
