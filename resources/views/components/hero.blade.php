<section class="relative overflow-hidden px-4 pb-16 pt-36 sm:px-6 sm:pb-24 sm:pt-44 lg:px-8 lg:pb-32 lg:pt-48">
    <div class="pointer-events-none absolute -left-28 top-36 h-72 w-72 rounded-full bg-blush/60 blur-3xl"></div>
    <div class="mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:gap-20">
        <div class="relative z-10 max-w-xl reveal">
            <p class="section-kicker">Pastelería artesanal en Jujuy</p>
            <h1 class="mt-6 max-w-2xl font-display text-5xl leading-[0.95] tracking-[-0.05em] text-bordo sm:text-7xl lg:text-[6rem]">Cada bocado,<br><span class="font-script text-[1.12em] font-normal text-rosa-viejo">una celebración</span></h1>
            <p class="mt-8 max-w-md text-base leading-7 text-ink/65 sm:text-lg">Tortas, cupcakes y boxes hechos por encargo para cumpleaños, meriendas y días que merecen algo dulce.</p>
            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a href="#catalogo" class="btn-primary">Elegir algo rico <span aria-hidden="true">↓</span></a>
                <a href="https://wa.me/{{ config('delicias.whatsapp') }}?text={{ rawurlencode('¡Hola! Quiero encargar algo rico en Delicias Sn.') }}" target="_blank" rel="noopener" class="btn-secondary">Pedir por WhatsApp <span aria-hidden="true">↗</span></a>
            </div>
            <div class="mt-10 flex flex-wrap items-center gap-x-3 gap-y-2 text-xs font-semibold text-ink/45"><span class="h-px w-10 bg-rosa-viejo"></span><span>Retiro en Jujuy 422</span><span aria-hidden="true">·</span><span>Envíos coordinados</span></div>
        </div>
        <div class="relative mx-auto w-full max-w-xl reveal [transition-delay:120ms]">
            <div class="absolute -right-2 -top-5 z-10 grid h-24 w-24 rotate-6 place-items-center rounded-full bg-caramelo p-3 text-center text-[10px] font-bold uppercase leading-4 tracking-[0.12em] text-white shadow-soft sm:-right-5 sm:h-28 sm:w-28"><span>Hecho<br>por encargo<br>♡</span></div>
            <div class="absolute -bottom-8 -left-7 z-10 hidden h-28 w-28 rounded-full border border-rosa-viejo/50 sm:block"></div>
            <div class="hero-cake-card overflow-hidden rounded-[3rem] rounded-bl-[7rem] border-[10px] border-white bg-blush shadow-soft sm:rounded-[4rem] sm:rounded-bl-[9rem]" role="img" aria-label="Diseño decorativo de Delicias Sn">
                <div class="hero-cake-mark"><span class="font-script text-6xl text-bordo">hecho<br>a mano</span><span class="mt-5 text-5xl text-caramelo">✦</span></div>
            </div>
            <div class="absolute -bottom-5 right-6 rounded-2xl bg-crema px-4 py-3 shadow-card sm:right-10"><p class="font-script text-2xl text-bordo">para celebrar</p><p class="text-[10px] font-bold uppercase tracking-[0.15em] text-rosa-viejo">lo que sea</p></div>
        </div>
    </div>
</section>
