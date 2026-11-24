@props(['order'])

@php
    $reviewTags = ['Riquísimo', 'Hermosa presentación', 'Llegó a tiempo', 'Muy buena atención', 'Volvería a pedir'];
@endphp

<div
    x-data="{
        reviewOpen: false,
        rating: @js((int) old('rating', 0)),
        hoverRating: 0,
        comment: @js(old('comment', '')),
        reviewSending: false,
        reviewErrors: {},
        moods: {
            1: ['Podemos hacerlo mejor', 'Contanos qué pasó, queremos mejorar.'],
            2: ['Nos queda por mejorar', 'Gracias por la sinceridad.'],
            3: ['Una experiencia agradable', '¡Vamos por más!'],
            4: ['¡Muy rico!', 'Nos alegra muchísimo.'],
            5: ['¡Una experiencia deliciosa!', 'Nos hiciste el día.'],
        },
        get shown() { return this.hoverRating || this.rating; },
        moodTitle() { return this.moods[this.shown]?.[0] || 'Tocá un cupcake para puntuar'; },
        moodText() { return this.moods[this.shown]?.[1] || 'De 1 a 5, ¿qué tan dulce fue?'; },
        reducedMotion() { return window.matchMedia('(prefers-reduced-motion: reduce)').matches; },
        openReview() {
            this.reviewOpen = true;
            document.documentElement.style.overflow = 'hidden';
            setTimeout(() => this.$refs.reviewStars?.querySelector('button')?.focus({ preventScroll: true }), 520);
        },
        closeReview() {
            if (this.reviewSending) return;
            this.reviewOpen = false;
            document.documentElement.style.overflow = '';
        },
        pickRating(value, el) {
            this.rating = value;
            delete this.reviewErrors.rating;
            if (this.reducedMotion()) return;
            el.animate(
                [{ transform: 'scale(1)' }, { transform: 'scale(1.3) rotate(-10deg)' }, { transform: 'scale(.92) rotate(4deg)' }, { transform: 'scale(1)' }],
                { duration: 480, easing: 'cubic-bezier(.34, 1.56, .64, 1)' }
            );
            this.burst(el, value);
        },
        burst(el, value) {
            const colors = ['#6b2d3c', '#c98b8b', '#c68b59', '#f4d9d5'];
            const count = 6 + value * 2;
            for (let i = 0; i < count; i++) {
                const dot = document.createElement('span');
                dot.className = 'review-burst';
                dot.style.background = colors[i % colors.length];
                el.appendChild(dot);
                const angle = (Math.PI * 2 * i) / count + Math.random() * .4;
                const distance = 28 + Math.random() * 22;
                dot.animate(
                    [
                        { transform: 'translate(-50%, -50%) rotate(0) scale(1)', opacity: 1 },
                        { transform: `translate(calc(-50% + ${Math.cos(angle) * distance}px), calc(-50% + ${Math.sin(angle) * distance}px)) rotate(${200 + i * 30}deg) scale(.4)`, opacity: 0 },
                    ],
                    { duration: 620 + Math.random() * 200, easing: 'cubic-bezier(.16, 1, .3, 1)' }
                ).onfinish = () => dot.remove();
            }
        },
        moveRating(step) {
            const next = Math.min(5, Math.max(1, (this.rating || 0) + step));
            const button = this.$refs.reviewStars.querySelectorAll('button')[next - 1];
            button.focus();
            this.pickRating(next, button);
        },
        hasTag(tag) { return this.comment.includes(tag); },
        toggleTag(tag) {
            if (this.hasTag(tag)) {
                this.comment = this.comment.replace(tag, '').replace(/\s*·\s*·\s*/g, ' · ').replace(/^\s*·\s*|\s*·\s*$/g, '').trim();
            } else {
                this.comment = this.comment.trim() ? `${this.comment.trim()} · ${tag}` : tag;
            }
            delete this.reviewErrors.comment;
        },
        validateReview() {
            this.reviewErrors = {};
            const name = this.$refs.reviewName.value.trim();
            const comment = this.comment.trim();

            if (!this.rating) this.reviewErrors.rating = 'Elegí de 1 a 5 cupcakes.';
            if (this.rating && !name) this.reviewErrors.name = 'Completá tu nombre para identificar tu opinión.';
            if (comment && comment.length < 10) this.reviewErrors.comment = 'El comentario debe tener al menos 10 caracteres.';

            const firstError = Object.keys(this.reviewErrors)[0];
            if (firstError === 'rating') {
                this.$refs.reviewStars?.querySelector('button')?.focus();
                if (!this.reducedMotion()) this.$refs.reviewStars.animate([{ transform: 'translateX(0)' }, { transform: 'translateX(-8px)' }, { transform: 'translateX(8px)' }, { transform: 'translateX(-4px)' }, { transform: 'translateX(0)' }], { duration: 380 });
            } else if (firstError) {
                this.$refs[`review${firstError.charAt(0).toUpperCase()}${firstError.slice(1)}`]?.focus();
            }

            return Object.keys(this.reviewErrors).length === 0;
        },
        trapFocus(event) {
            const focusable = Array.from(this.$el.querySelectorAll('button, input, textarea, [href]')).filter((element) => !element.disabled && element.offsetParent !== null);
            if (!focusable.length) return;

            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        },
    }"
    x-init="setTimeout(() => openReview(), @js($errors->any() || session('review_status') ? 150 : 400))"
    @keydown.escape.window="closeReview()"
>
    <div x-show="reviewOpen" x-cloak
         x-transition:enter="review-backdrop-enter" x-transition:enter-start="review-backdrop-hidden" x-transition:enter-end="review-backdrop-shown"
         x-transition:leave="review-backdrop-leave" x-transition:leave-start="review-backdrop-shown" x-transition:leave-end="review-backdrop-hidden"
         class="review-modal-backdrop" @click.self="closeReview()" role="dialog" aria-modal="true" aria-labelledby="confirmed-review-title" aria-describedby="confirmed-review-description">
        <div x-show="reviewOpen"
             x-transition:enter="review-panel-enter" x-transition:enter-start="review-panel-hidden" x-transition:enter-end="review-panel-shown"
             x-transition:leave="review-panel-leave" x-transition:leave-start="review-panel-shown" x-transition:leave-end="review-panel-out"
             :class="reviewOpen && 'review-panel-open'" class="review-modal-panel" @keydown.tab="trapFocus($event)" @click.stop>

            <div class="review-hero">
                <span class="review-hero-blob review-hero-blob-a" aria-hidden="true"></span>
                <span class="review-hero-blob review-hero-blob-b" aria-hidden="true"></span>
                <header class="review-modal-header review-stagger" style="--i: 0">
                    <div class="flex items-center gap-3">
                        <x-brand-mark size="h-11 w-11" class="review-brand" />
                        <div>
                            <p class="review-script">Tu opinión</p>
                            <h2 id="confirmed-review-title" class="review-modal-title">¿Qué tan dulce fue?</h2>
                        </div>
                    </div>
                    <button type="button" @click="closeReview()" class="review-modal-close" aria-label="Cerrar valoración">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                    </button>
                </header>
            </div>

            <div class="review-body">
                @if (session('review_status'))
                    <div class="review-success review-stagger" style="--i: 1" role="status">
                        <span class="review-success-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                        </span>
                        <div><p class="font-bold text-bordo">¡Gracias por tu valoración!</p><p class="mt-1 text-sm leading-6 text-ink/65">{{ session('review_status') }}</p></div>
                    </div>
                    <button type="button" @click="closeReview()" class="btn-primary mt-6 w-full review-stagger" style="--i: 2">Continuar</button>
                @else
                    <p id="confirmed-review-description" class="review-modal-intro review-stagger" style="--i: 1">Cada valoración nos ayuda a seguir horneando con cariño.</p>

                    <form action="{{ route('reviews.store') }}" method="POST" class="review-form" @submit="if (!validateReview()) { $event.preventDefault(); } else { reviewSending = true; }" novalidate>
                        @csrf
                        <input type="hidden" name="token" value="{{ $order->access_token }}">
                        <fieldset class="review-fieldset review-rating-card review-stagger" style="--i: 2">
                            <legend class="sr-only">Tu valoración</legend>
                            <input type="hidden" name="rating" :value="rating">

                            <div x-ref="reviewStars" class="review-cupcakes" role="radiogroup" aria-label="Elegir cantidad de cupcakes"
                                 @mouseleave="hoverRating = 0"
                                 @keydown.arrow-right.prevent="moveRating(1)" @keydown.arrow-up.prevent="moveRating(1)"
                                 @keydown.arrow-left.prevent="moveRating(-1)" @keydown.arrow-down.prevent="moveRating(-1)">
                                @for ($cupcake = 1; $cupcake <= 5; $cupcake++)
                                    <button type="button"
                                            @click="pickRating({{ $cupcake }}, $el)"
                                            @mouseenter="hoverRating = {{ $cupcake }}"
                                            :aria-checked="(rating === {{ $cupcake }}).toString()"
                                            :tabindex="(rating ? rating === {{ $cupcake }} : {{ $cupcake }} === 1) ? 0 : -1"
                                            :class="{ 'is-filled': shown >= {{ $cupcake }}, 'is-preview': hoverRating && hoverRating >= {{ $cupcake }} && rating < {{ $cupcake }} }"
                                            class="review-cupcake" style="--s: {{ $cupcake }}" role="radio"
                                            aria-label="{{ $cupcake }} {{ $cupcake === 1 ? 'cupcake' : 'cupcakes' }}">
                                        <svg viewBox="0 0 48 48" aria-hidden="true">
                                            <path class="cc-stem" d="M24 9.5c.6-2.6 2-4.2 4.2-5" fill="none" stroke-width="1.6" stroke-linecap="round"/>
                                            <path class="cc-wrapper" d="M11.5 27h25l-3.4 14.6a2.4 2.4 0 0 1-2.3 1.9H17.2a2.4 2.4 0 0 1-2.3-1.9Z"/>
                                            <path class="cc-ridges" d="M18 28.5 19.5 42M24 28.5V42M30 28.5 28.5 42" fill="none" stroke-width="1.3" stroke-linecap="round"/>
                                            <path class="cc-frosting" d="M8.6 27.4c-1.9-4 .9-7.6 4.6-7.7.1-4.5 4-7.6 8.1-6.6 1-1.4 2.3-2.1 2.7-2.1s1.7.7 2.7 2.1c4.1-1 8 2.1 8.1 6.6 3.7.1 6.5 3.7 4.6 7.7Z"/>
                                            <g class="cc-sprinkles" stroke-width="1.6" stroke-linecap="round">
                                                <path d="M15 22.5l1.6-1"/><path d="M21 18l.4 1.8"/><path d="M27.5 17.5l1.5.9"/><path d="M33 22.8l-1.4-1.2"/><path d="M24 23.5l1.7-.3"/>
                                            </g>
                                            <circle class="cc-cherry" cx="24" cy="10.5" r="3.4"/>
                                        </svg>
                                    </button>
                                @endfor
                            </div>

                            <div class="review-mood" aria-live="polite">
                                <p class="review-mood-title" x-text="moodTitle()" :class="shown && 'is-active'"></p>
                                <p class="review-mood-text" x-text="moodText()"></p>
                            </div>
                            <div class="review-meter" aria-hidden="true"><span :style="`width: ${shown * 20}%`"></span></div>

                            <p x-show="reviewErrors.rating" x-text="reviewErrors.rating" class="review-error text-center" role="alert"></p>
                            @error('rating')<p class="review-error text-center" role="alert">{{ $message }}</p>@enderror
                        </fieldset>

                        <div x-show="rating" x-cloak
                             x-transition:enter="review-reveal-enter" x-transition:enter-start="review-reveal-hidden" x-transition:enter-end="review-reveal-shown"
                             class="review-details">
                            <div>
                                <span class="review-label">¿Qué te gustó?</span>
                                <div class="review-tags">
                                    @foreach ($reviewTags as $tag)
                                        <button type="button" @click="toggleTag(@js($tag))" :aria-pressed="hasTag(@js($tag)).toString()" :class="hasTag(@js($tag)) && 'is-on'" class="review-tag" style="--t: {{ $loop->index }}">{{ $tag }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <label class="review-field">
                                <span class="review-label">Tu nombre</span>
                                <input x-ref="reviewName" name="name" value="{{ old('name') }}" maxlength="80" class="review-input" placeholder="¿Cómo te llamás?" autocomplete="name" @input="delete reviewErrors.name">
                                <span x-show="reviewErrors.name" x-text="reviewErrors.name" class="review-error" role="alert"></span>
                                @error('name')<span class="review-error" role="alert">{{ $message }}</span>@enderror
                            </label>

                            <label class="review-field">
                                <span class="review-label flex justify-between">Comentario <small x-text="`${comment.length}/600`"></small></span>
                                <textarea x-ref="reviewComment" name="comment" x-model="comment" maxlength="600" rows="4" class="review-input review-textarea" placeholder="Contanos qué te pareció... (opcional)"></textarea>
                                <span x-show="reviewErrors.comment" x-text="reviewErrors.comment" class="review-error" role="alert"></span>
                                @error('comment')<span class="review-error" role="alert">{{ $message }}</span>@enderror
                            </label>
                        </div>

                        <button type="submit" :disabled="reviewSending" :aria-busy="reviewSending.toString()" :class="rating && 'is-ready'" class="btn-primary review-submit review-stagger" style="--i: 3">
                            <span x-show="!reviewSending">Enviar valoración <span aria-hidden="true">→</span></span>
                            <span x-show="reviewSending" x-cloak class="inline-flex items-center gap-2"><span class="review-spinner" aria-hidden="true"></span>Enviando...</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('head')
<style>
    .review-modal-backdrop { align-items: center; background: rgba(59, 42, 42, .55); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); display: flex; inset: 0; justify-content: center; overflow-y: auto; padding: 1rem; position: fixed; z-index: 70; }
    .review-modal-panel { background: #fbf5ee; border: 1px solid rgba(255, 255, 255, .8); border-radius: 2rem; box-shadow: 0 30px 90px -20px rgba(59, 42, 42, .45); margin: auto; max-height: calc(100dvh - 2rem); max-width: 30rem; overflow-x: hidden; overflow-y: auto; transform-origin: 50% 85%; width: 100%; will-change: transform, opacity; }

    .review-hero { background: linear-gradient(135deg, #6b2d3c 0%, #874152 100%); overflow: hidden; padding: 1.4rem 1.4rem 2.6rem; position: relative; }
    .review-hero-blob { border-radius: 999px; filter: blur(22px); position: absolute; }
    .review-hero-blob-a { background: rgba(201, 139, 139, .45); height: 8rem; right: -2rem; top: -3rem; width: 8rem; }
    .review-hero-blob-b { background: rgba(198, 139, 89, .35); bottom: -4rem; height: 9rem; left: -2rem; width: 9rem; }
    .review-modal-header { align-items: flex-start; display: flex; gap: 1rem; justify-content: space-between; position: relative; }
    .review-brand { background: #fbf5ee; border-radius: 999px; flex: 0 0 auto; height: 2.75rem; object-fit: contain; padding: .3rem; width: 2.75rem; }
    .review-script { color: #f4d9d5; font-family: var(--font-script); font-size: 1.65rem; line-height: 1.1; margin-bottom: .2rem; }
    .review-modal-title { color: #fbf5ee; font-family: var(--font-display); font-size: clamp(1.45rem, 5vw, 1.8rem); line-height: 1.15; }
    .review-modal-close { align-items: center; background: rgba(255, 255, 255, .14); border: 1px solid rgba(255, 255, 255, .2); border-radius: 999px; color: #fbf5ee; display: grid; flex: 0 0 auto; height: 2.4rem; justify-content: center; transition: background .2s, transform .3s var(--ease-brand); width: 2.4rem; }
    .review-modal-close:hover { background: rgba(255, 255, 255, .26); transform: rotate(90deg); }
    .review-modal-close:focus-visible { outline: 2px solid #fbf5ee; outline-offset: 3px; }

    .review-body { background: #fbf5ee; border-radius: 1.5rem 1.5rem 0 0; margin-top: -1.5rem; padding: 1.15rem 1.25rem 1.4rem; position: relative; }
    .review-modal-intro { color: rgba(59, 42, 42, .62); font-size: .88rem; line-height: 1.6; text-align: center; }
    .review-form { display: grid; gap: 1.1rem; margin-top: 1rem; }
    .review-fieldset { border: 0; margin: 0; min-width: 0; padding: 0; }
    .review-rating-card { background: #fff; border: 1px solid rgba(107, 45, 60, .08); border-radius: 1.6rem; box-shadow: 0 12px 30px -18px rgba(107, 45, 60, .35); padding: 1.1rem 1rem 1rem; }
    .review-label { color: #6b2d3c; display: block; font-size: .7rem; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
    .review-label small { color: rgba(59, 42, 42, .4); font-size: .7rem; font-weight: 600; letter-spacing: 0; }

    /* Cupcakes */
    .review-cupcakes { display: flex; gap: .2rem; justify-content: center; }
    .review-cupcake { border-radius: 1rem; display: grid; height: 3.6rem; place-items: center; position: relative; transition: transform .35s cubic-bezier(.34, 1.56, .64, 1); width: 3.6rem; }
    .review-cupcake svg { height: 2.9rem; overflow: visible; transition: filter .3s, transform .35s cubic-bezier(.34, 1.56, .64, 1); width: 2.9rem; }
    .review-cupcake:hover svg { transform: translateY(-4px) rotate(-4deg); }
    .review-cupcake:focus-visible { box-shadow: 0 0 0 3px rgba(201, 139, 139, .45); outline: 0; }
    .review-cupcake .cc-wrapper, .review-cupcake .cc-frosting, .review-cupcake .cc-cherry { fill: #f6efe9; stroke: rgba(107, 45, 60, .22); stroke-width: 1.4; transition: fill .3s, stroke .3s; }
    .review-cupcake .cc-ridges, .review-cupcake .cc-stem { stroke: rgba(107, 45, 60, .18); transition: stroke .3s; }
    .review-cupcake .cc-sprinkles { opacity: 0; stroke: #fff; transition: opacity .3s; }
    .review-cupcake.is-filled .cc-wrapper { fill: #c68b59; stroke: #a9713f; }
    .review-cupcake.is-filled .cc-ridges { stroke: rgba(255, 255, 255, .45); }
    .review-cupcake.is-filled .cc-frosting { fill: #f4d9d5; stroke: #c98b8b; }
    .review-cupcake.is-filled .cc-cherry { fill: #6b2d3c; stroke: #6b2d3c; }
    .review-cupcake.is-filled .cc-stem { stroke: #6b2d3c; }
    .review-cupcake.is-filled .cc-sprinkles { opacity: 1; }
    .review-cupcake.is-filled .cc-sprinkles path:nth-child(odd) { stroke: #c98b8b; }
    .review-cupcake.is-filled .cc-sprinkles path:nth-child(even) { stroke: #6b2d3c; }
    .review-cupcake.is-filled svg { filter: drop-shadow(0 6px 8px rgba(107, 45, 60, .22)); }
    .review-cupcake.is-preview svg { opacity: .7; }
    .review-cupcake[aria-checked="true"] svg { transform: translateY(-3px); }
    .review-burst { border-radius: 2px; height: .5rem; left: 50%; pointer-events: none; position: absolute; top: 45%; width: .2rem; }

    .review-mood { margin-top: .55rem; min-height: 2.6rem; text-align: center; }
    .review-mood-title { color: rgba(59, 42, 42, .5); font-family: var(--font-display); font-size: 1.05rem; transition: color .25s; }
    .review-mood-title.is-active { color: #6b2d3c; }
    .review-mood-text { color: rgba(59, 42, 42, .5); font-size: .78rem; margin-top: .1rem; }
    .review-meter { background: #f6efe9; border-radius: 999px; height: .4rem; margin: .7rem auto 0; max-width: 13rem; overflow: hidden; }
    .review-meter span { background: linear-gradient(90deg, #f4d9d5, #c98b8b 55%, #6b2d3c); border-radius: inherit; display: block; height: 100%; transition: width .5s var(--ease-brand); }

    /* Detalles revelados */
    .review-details { display: grid; gap: 1.1rem; }
    .review-reveal-enter { transition: opacity .45s ease-out, transform .55s var(--ease-brand); }
    .review-reveal-hidden { opacity: 0; transform: translateY(-.6rem); }
    .review-reveal-shown { opacity: 1; transform: none; }
    .review-tags { display: flex; flex-wrap: wrap; gap: .45rem; margin-top: .6rem; }
    .review-tag { background: #fff; border: 1px solid rgba(107, 45, 60, .14); border-radius: 999px; color: rgba(59, 42, 42, .75); font-size: .78rem; font-weight: 600; padding: .45rem .8rem; transition: background .2s, color .2s, border-color .2s, transform .25s cubic-bezier(.34, 1.56, .64, 1); }
    .review-tag:hover { border-color: #c98b8b; transform: translateY(-1px); }
    .review-tag.is-on { background: #6b2d3c; border-color: #6b2d3c; color: #fff; }
    .review-tag.is-on::before { content: '✓ '; }
    .review-tag:active { transform: scale(.95); }
    .review-details .review-tag { animation: review-rise .45s var(--ease-brand) backwards; animation-delay: calc(.05s + var(--t, 0) * .04s); }

    .review-field { display: block; min-width: 0; }
    .review-input { background: #fff; border: 1px solid rgba(107, 45, 60, .16); border-radius: 1rem; color: #3b2a2a; display: block; font-size: .95rem; line-height: 1.5; margin-top: .5rem; outline: 0; padding: .8rem 1rem; transition: border-color .18s, box-shadow .18s; width: 100%; }
    .review-input::placeholder { color: rgba(59, 42, 42, .4); }
    .review-input:hover { border-color: rgba(107, 45, 60, .3); }
    .review-input:focus { border-color: #c98b8b; box-shadow: 0 0 0 3px rgba(201, 139, 139, .2); }
    .review-textarea { min-height: 6.5rem; resize: vertical; }
    .review-error { color: #a33f42; display: block; font-size: .75rem; font-weight: 650; line-height: 1.4; margin-top: .45rem; }
    .review-submit { border: 0; min-height: 3.25rem; opacity: .75; transition: opacity .3s, transform .3s, box-shadow .3s; width: 100%; }
    .review-submit.is-ready { box-shadow: 0 14px 28px -14px rgba(107, 45, 60, .8); opacity: 1; }
    .review-submit:disabled { cursor: wait; opacity: .68; transform: none; }
    .review-spinner { animation: review-spin .7s linear infinite; border: 2px solid rgba(255, 255, 255, .4); border-radius: 999px; border-top-color: #fff; height: 1rem; width: 1rem; }
    .review-success { align-items: flex-start; background: rgba(238, 248, 239, .9); border: 1px solid rgba(76, 128, 96, .2); border-radius: 1.25rem; display: flex; gap: .75rem; padding: 1rem; }
    .review-success-icon { align-items: center; background: #4c8060; border-radius: 999px; color: #fff; display: grid; flex: 0 0 auto; height: 1.6rem; justify-content: center; width: 1.6rem; }

    /* Entrada del modal */
    .review-backdrop-enter { transition: opacity .45s ease-out, backdrop-filter .45s ease-out, -webkit-backdrop-filter .45s ease-out; }
    .review-backdrop-leave { transition: opacity .28s ease-in .06s, backdrop-filter .28s ease-in .06s, -webkit-backdrop-filter .28s ease-in .06s; }
    .review-backdrop-hidden { opacity: 0; -webkit-backdrop-filter: blur(0); backdrop-filter: blur(0); }
    .review-backdrop-shown { opacity: 1; }
    .review-panel-enter { transition: opacity .35s ease-out, transform .6s cubic-bezier(.22, 1.25, .36, 1); }
    .review-panel-leave { transition: opacity .22s ease-in, transform .26s cubic-bezier(.4, 0, 1, 1); }
    .review-panel-hidden { opacity: 0; transform: translateY(2.5rem) scale(.92); }
    .review-panel-shown { opacity: 1; transform: none; }
    .review-panel-out { opacity: 0; transform: translateY(1rem) scale(.97); }
    .review-panel-open .review-stagger { animation: review-rise .55s cubic-bezier(.22, 1, .36, 1) backwards; animation-delay: calc(.12s + var(--i, 0) * .07s); }
    .review-panel-open .review-cupcake { animation: review-cupcake-in .55s cubic-bezier(.34, 1.56, .64, 1) backwards; animation-delay: calc(.32s + var(--s, 1) * .07s); }

    @keyframes review-rise { from { opacity: 0; transform: translateY(.9rem); } to { opacity: 1; transform: none; } }
    @keyframes review-cupcake-in { from { opacity: 0; transform: translateY(.8rem) scale(.4) rotate(-20deg); } to { opacity: 1; transform: none; } }
    @keyframes review-spin { to { transform: rotate(360deg); } }

    @media (min-width: 640px) {
        .review-modal-backdrop { padding: 1.5rem; }
        .review-hero { padding: 1.75rem 1.9rem 2.8rem; }
        .review-body { padding: 1.3rem 1.9rem 1.9rem; }
        .review-cupcakes { gap: .5rem; }
    }
    @media (prefers-reduced-motion: reduce) {
        .review-backdrop-enter, .review-backdrop-leave, .review-panel-enter, .review-panel-leave, .review-reveal-enter { transition: opacity .2s linear; }
        .review-panel-hidden, .review-panel-out, .review-reveal-hidden { transform: none; }
        .review-panel-open .review-stagger, .review-panel-open .review-cupcake, .review-details .review-tag { animation: none; }
        .review-cupcake, .review-cupcake svg, .review-meter span { transition: none; }
    }
</style>
@endpush
