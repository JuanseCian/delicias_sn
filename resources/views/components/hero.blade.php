<section class="relative overflow-hidden px-4 pb-16 pt-32 sm:px-6 sm:pb-24 sm:pt-40 lg:px-8 lg:pb-28 lg:pt-44">
    <div class="pointer-events-none absolute -left-28 top-36 h-72 w-72 rounded-full bg-blush/60 blur-3xl"></div>
    <div class="mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:gap-16">
        <div class="relative z-10 max-w-xl reveal">
            <p class="section-kicker">Pastelería artesanal · Jujuy 422</p>
            <h1 class="mt-6 font-display text-5xl leading-[0.98] tracking-[-0.04em] text-bordo sm:text-7xl lg:text-[5.5rem]">Cada bocado,<br><span class="font-script text-[1.12em] font-normal text-rosa-viejo">una celebración</span></h1>
            <p class="mt-7 max-w-md text-base leading-7 text-ink/65 sm:text-lg">Hacemos tortas, boxes y dulces con tiempo, mimo y los mejores ingredientes. Porque las fechas lindas merecen algo hecho especialmente para vos.</p>
            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a href="#catalogo" class="btn-primary">Ver productos <span aria-hidden="true">↓</span></a>
                <a href="https://wa.me/{{ config('delicias.whatsapp') }}?text={{ rawurlencode('¡Hola! Quiero encargar algo rico en Delicias Sn.') }}" target="_blank" rel="noopener" class="btn-secondary">Encargar por WhatsApp <span aria-hidden="true">↗</span></a>
            </div>
            <div class="mt-10 flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.15em] text-ink/45"><span class="h-px w-10 bg-rosa-viejo"></span><span>Hecho a mano, desde 2018</span></div>
        </div>
        <div class="relative mx-auto w-full max-w-xl reveal [transition-delay:120ms]">
            <div class="absolute -right-2 -top-5 z-10 grid h-24 w-24 rotate-6 place-items-center rounded-full bg-caramelo p-3 text-center text-[10px] font-bold uppercase leading-4 tracking-[0.12em] text-white shadow-soft sm:-right-5 sm:h-28 sm:w-28"><span>Hecho<br>con amor<br>♡</span></div>
            <div class="absolute -bottom-8 -left-7 z-10 hidden h-28 w-28 rounded-full border border-rosa-viejo/50 sm:block"></div>
            <div class="hero-cake-card overflow-hidden rounded-[3rem] rounded-bl-[7rem] border-[10px] border-white bg-blush shadow-soft sm:rounded-[4rem] sm:rounded-bl-[9rem]" role="img" aria-label="Diseño decorativo de Delicias Sn">
                <div class="hero-cake-mark"><span class="font-script text-6xl text-bordo">hecho<br>a mano</span><span class="mt-5 text-5xl text-caramelo">✦</span></div>
            </div>
            <div class="absolute -bottom-5 right-6 rounded-2xl bg-crema px-4 py-3 shadow-card sm:right-10"><p class="font-script text-2xl text-bordo">para celebrar</p><p class="text-[10px] font-bold uppercase tracking-[0.15em] text-rosa-viejo">lo que sea</p></div>
        </div>
    </div>
</section>
