<section id="opiniones" class="scroll-mt-28 bg-white/55 px-4 py-20 sm:px-6 sm:py-28 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-end">
            <div class="reveal">
                <p class="section-kicker">Lo que dicen</p>
                <h2 class="mt-4 font-display text-4xl leading-tight tracking-[-0.03em] text-bordo sm:text-5xl">Historias<br><span class="font-script text-6xl font-normal text-rosa-viejo">que endulzan</span></h2>
                <p class="mt-5 max-w-md text-base leading-7 text-ink/65">Después de tu encargo podés dejarnos tu opinión. Nos ayuda a seguir mejorando cada detalle.</p>
            </div>
            <div class="rounded-[2rem] bg-bordo p-6 text-crema shadow-soft sm:p-8 reveal">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div><p class="eyebrow !text-blush">Valoración general</p><div class="mt-2 flex items-center gap-3"><span class="font-display text-5xl">{{ $stats['average'] > 0 ? number_format($stats['average'], 1, ',', '.') : '—' }}</span><div><div class="flex gap-0.5 text-xl text-caramelo" aria-label="{{ $stats['average'] }} de 5 estrellas">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= round($stats['average']) ? '' : 'opacity-30' }}">★</span>@endfor</div><p class="mt-1 text-xs text-crema/55">{{ $stats['count'] }} {{ $stats['count'] === 1 ? 'opinión publicada' : 'opiniones publicadas' }}</p></div></div></div>
                    <p class="max-w-xs text-sm leading-6 text-crema/70">Cada comentario se revisa antes de aparecer públicamente.</p>
                </div>
            </div>
        </div>

        <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($reviews as $review)
                <article class="rounded-[1.75rem] border border-bordo/10 bg-crema p-5 shadow-card sm:p-6">
                    <div class="flex items-center justify-between gap-3"><div class="flex gap-0.5 text-lg text-caramelo" aria-label="{{ $review->rating }} de 5 estrellas">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $review->rating ? '' : 'opacity-25' }}">★</span>@endfor</div><span class="text-xs text-ink/40">{{ $review->created_at->locale('es')->translatedFormat('M Y') }}</span></div>
                    <blockquote class="mt-4 text-sm leading-7 text-ink/75">“{{ $review->comment }}”</blockquote>
                    <p class="mt-5 text-xs font-bold uppercase tracking-[0.12em] text-bordo">{{ $review->name }}</p>
                </article>
            @empty
                <div class="rounded-[1.75rem] border border-dashed border-bordo/15 bg-crema p-6 text-sm leading-6 text-ink/60 md:col-span-2 lg:col-span-3">Todavía no hay opiniones publicadas. Sé la primera persona en compartir una.</div>
            @endforelse
        </div>

    </div>
</section>
