<section id="opiniones" x-data="autoCarousel({{ $reviews->count() }}, 2000)" @mouseenter="pause()" @mouseleave="resume()" @focusin="pause()" @focusout="resumeIfFocusLeaves($event)" class="scroll-mt-28 bg-white/55 px-4 py-20 sm:px-6 sm:py-28 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-10 lg:grid-cols-[0.7fr_1.3fr] lg:items-start">
            <div class="reveal">
                <p class="section-kicker">Lo que dicen</p>
                <h1 class="mt-4 font-display text-4xl leading-tight tracking-[-0.03em] text-bordo sm:text-5xl">Historias<br><span class="font-script text-6xl font-normal text-rosa-viejo">que endulzan</span></h1>
                <p class="mt-5 max-w-md text-base leading-7 text-ink/65">Después de tu encargo podés dejarnos tu opinión. Nos ayuda a seguir mejorando cada detalle.</p>
            </div>

            <div>
        <div class="relative max-w-3xl overflow-hidden" aria-live="polite" aria-atomic="true">
            @if ($reviews->isNotEmpty())
                <div class="flex will-change-transform transition-transform duration-700 ease-[cubic-bezier(.16,1,.3,1)]" :style="`transform: translateX(-${active * 100}%)`">
                    @foreach ($reviews as $review)
                        <article class="w-full shrink-0 rounded-[1.75rem] border border-bordo/10 bg-crema p-6 shadow-card sm:p-9">
                    <div class="flex items-center justify-between gap-3"><div class="flex gap-0.5 text-lg text-caramelo" aria-label="{{ $review->rating }} de 5 estrellas">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $review->rating ? '' : 'opacity-25' }}">★</span>@endfor</div><span class="text-xs text-ink/40">{{ $review->created_at->locale('es')->translatedFormat('M Y') }}</span></div>
                    <blockquote class="mt-5 font-display text-2xl leading-tight text-ink sm:text-3xl">“{{ $review->comment }}”</blockquote>
                    <p class="mt-7 text-xs font-bold uppercase tracking-[0.12em] text-bordo">{{ $review->name }}</p>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-[1.75rem] border border-dashed border-bordo/15 bg-crema p-6 text-sm leading-6 text-ink/60">Todavía no hay opiniones publicadas. Sé la primera persona en compartir una.</div>
            @endif
        </div>

        @if ($reviews->count() > 1)
            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" @click="previous()" class="grid h-10 w-10 place-items-center rounded-full border border-bordo/15 text-bordo transition hover:border-bordo/40 hover:bg-blush" aria-label="Opinión anterior">
                    <span aria-hidden="true">←</span>
                </button>
                <div class="flex items-center gap-2" role="tablist" aria-label="Seleccionar opinión">
                    @foreach ($reviews as $review)
                        <button type="button" @click="goTo({{ $loop->index }})" :aria-selected="active === {{ $loop->index }}" :class="active === {{ $loop->index }} ? 'w-7 bg-bordo' : 'w-2 bg-rosa-viejo/40'" class="h-2 rounded-full transition-all" role="tab" aria-label="Ver opinión {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
                <button type="button" @click="next()" class="grid h-10 w-10 place-items-center rounded-full border border-bordo/15 text-bordo transition hover:border-bordo/40 hover:bg-blush" aria-label="Opinión siguiente">
                    <span aria-hidden="true">→</span>
                </button>
            </div>
        @endif
            </div>

        </div>
    </div>
</section>
