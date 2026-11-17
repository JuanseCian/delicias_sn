<div
    x-data="{
        reviewOpen: true,
        rating: @js((int) old('rating', 0)),
        reviewSending: false,
        reviewErrors: {},
        ratingLabel() {
            return {
                1: 'Podemos hacerlo mejor',
                2: 'Nos queda mucho por mejorar',
                3: 'Una experiencia agradable',
                4: 'Una experiencia muy linda',
                5: '¡Una experiencia deliciosa!',
            }[this.rating] || 'Elegí una puntuación';
        },
        validateReview() {
            this.reviewErrors = {};
            const name = this.$refs.reviewName.value.trim();
            const comment = this.$refs.reviewComment.value.trim();

            if (!this.rating) this.reviewErrors.rating = 'Elegí una puntuación de 1 a 5 estrellas.';
            if (!name) this.reviewErrors.name = 'Completá tu nombre para identificar tu opinión.';
            if (comment && comment.length < 10) this.reviewErrors.comment = 'El comentario debe tener al menos 10 caracteres.';

            const firstError = Object.keys(this.reviewErrors)[0];
            if (firstError === 'rating') {
                this.$refs.reviewStars?.querySelector('button')?.focus();
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
    x-init="$nextTick(() => $refs.reviewStars?.querySelector('button')?.focus())"
    @keydown.escape.window="if (!reviewSending) reviewOpen = false"
>
    <div x-show="reviewOpen" x-cloak x-transition.opacity class="review-modal-backdrop" @click.self="if (!reviewSending) reviewOpen = false" role="dialog" aria-modal="true" aria-labelledby="confirmed-review-title" aria-describedby="confirmed-review-description">
        <div x-show="reviewOpen" x-transition class="review-modal-panel" @keydown.tab="trapFocus($event)" @click.stop>
            <header class="review-modal-header">
                <div>
                    <p class="eyebrow">Tu opinión</p>
                    <h2 id="confirmed-review-title" class="review-modal-title">¿Qué te pareció?</h2>
                </div>
                <button type="button" @click="if (!reviewSending) reviewOpen = false" class="review-modal-close" aria-label="Cerrar valoración">&times;</button>
            </header>

            @if (session('review_status'))
                <div class="review-success" role="status">
                    <span class="review-success-icon" aria-hidden="true">✓</span>
                    <div><p class="font-bold text-bordo">¡Gracias por tu valoración!</p><p class="mt-1 text-sm leading-6 text-ink/65">{{ session('review_status') }}</p></div>
                </div>
                <button type="button" @click="reviewOpen = false" class="btn-primary mt-6 w-full">Continuar</button>
            @else
                <p id="confirmed-review-description" class="review-modal-intro">Tu experiencia nos ayuda a seguir haciendo cada detalle con cariño. Elegí tus estrellas y, si querés, dejanos un comentario.</p>

                <form action="{{ route('reviews.store') }}" method="POST" class="review-form" @submit="if (!validateReview()) { $event.preventDefault(); } else { reviewSending = true; }" novalidate>
                    @csrf
                    <fieldset class="review-fieldset">
                        <legend class="review-label text-center">Tu valoración</legend>
                        <input type="hidden" name="rating" :value="rating">
                        <div x-ref="reviewStars" class="review-stars" role="radiogroup" aria-label="Elegir cantidad de estrellas">
                            @for ($star = 1; $star <= 5; $star++)
                                <button type="button" @click="rating = {{ $star }}" :aria-checked="(rating === {{ $star }}).toString()" :class="rating >= {{ $star }} ? 'review-star-selected' : ''" class="review-star" role="radio" aria-label="{{ $star }} {{ $star === 1 ? 'estrella' : 'estrellas' }}">★</button>
                            @endfor
                        </div>
                        <p class="review-rating-description" x-text="ratingLabel()" :class="rating ? 'review-rating-active' : ''"></p>
                        <p x-show="reviewErrors.rating" x-text="reviewErrors.rating" class="review-error text-center" role="alert"></p>
                        @error('rating')<p class="review-error text-center" role="alert">{{ $message }}</p>@enderror
                    </fieldset>

                    <label class="review-field">
                        <span class="review-label">Tu nombre</span>
                        <input x-ref="reviewName" name="name" value="{{ old('name') }}" maxlength="80" class="review-input" placeholder="¿Cómo te llamás?" autocomplete="name">
                        <span x-show="reviewErrors.name" x-text="reviewErrors.name" class="review-error" role="alert"></span>
                        @error('name')<span class="review-error" role="alert">{{ $message }}</span>@enderror
                    </label>

                    <label class="review-field">
                        <span class="review-label">Comentario <small>(opcional)</small></span>
                        <textarea x-ref="reviewComment" name="comment" maxlength="600" rows="5" class="review-input review-textarea" placeholder="Contanos qué te pareció...">{{ old('comment') }}</textarea>
                        <span x-show="reviewErrors.comment" x-text="reviewErrors.comment" class="review-error" role="alert"></span>
                        @error('comment')<span class="review-error" role="alert">{{ $message }}</span>@enderror
                    </label>

                    <button type="submit" :disabled="reviewSending" :aria-busy="reviewSending.toString()" class="btn-primary review-submit">
                        <span x-show="!reviewSending">Enviar valoración <span aria-hidden="true">→</span></span>
                        <span x-show="reviewSending" x-cloak class="inline-flex items-center gap-2"><span class="review-spinner" aria-hidden="true"></span>Enviando...</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@push('head')
<style>
    .review-modal-backdrop { align-items: center; background: rgba(59, 42, 42, .58); display: flex; inset: 0; justify-content: center; overflow-y: auto; padding: 1rem; position: fixed; z-index: 70; }
    .review-modal-panel { background: #fbf5ee; border: 1px solid rgba(255, 255, 255, .8); border-radius: 2rem; box-shadow: 0 24px 80px rgba(59, 42, 42, .24); margin: auto; max-height: calc(100dvh - 2rem); max-width: 31rem; overflow-y: auto; padding: 1.25rem; width: 100%; }
    .review-modal-header { align-items: flex-start; display: flex; gap: 1rem; justify-content: space-between; }
    .review-modal-title { color: #6b2d3c; font-family: var(--font-display); font-size: clamp(1.8rem, 6vw, 2.35rem); line-height: 1.1; margin-top: .5rem; }
    .review-modal-close { align-items: center; background: #f4d9d5; border: 1px solid rgba(107, 45, 60, .12); border-radius: 999px; color: #6b2d3c; display: grid; flex: 0 0 auto; font-size: 1.65rem; height: 2.5rem; justify-content: center; line-height: 1; transition: background .18s, transform .18s; width: 2.5rem; }
    .review-modal-close:hover { background: #c98b8b; color: #fff; transform: rotate(5deg); }
    .review-modal-close:focus-visible { outline: 2px solid #6b2d3c; outline-offset: 3px; }
    .review-modal-intro { color: rgba(59, 42, 42, .66); font-size: .9rem; line-height: 1.65; margin-top: 1rem; }
    .review-form { display: grid; gap: 1.25rem; margin-top: 1.5rem; }
    .review-fieldset { border: 0; margin: 0; min-width: 0; padding: 0; }
    .review-label { color: #6b2d3c; display: block; font-size: .72rem; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
    .review-label small { color: rgba(59, 42, 42, .45); font-size: .68rem; font-weight: 600; letter-spacing: 0; text-transform: none; }
    .review-stars { align-items: center; display: flex; gap: .45rem; justify-content: center; margin-top: .75rem; }
    .review-star { align-items: center; background: rgba(255, 255, 255, .7); border: 1px solid rgba(198, 139, 89, .28); border-radius: 999px; color: #c68b59; display: grid; font-size: 1.7rem; height: 3.15rem; justify-content: center; line-height: 1; transition: background .18s, color .18s, transform .18s, box-shadow .18s; width: 3.15rem; }
    .review-star:hover, .review-star-selected { background: #c68b59; color: #fff; transform: translateY(-2px); }
    .review-star:focus-visible { box-shadow: 0 0 0 3px rgba(201, 139, 139, .45); outline: 0; }
    .review-rating-description { color: rgba(59, 42, 42, .42); font-size: .78rem; line-height: 1.4; margin-top: .65rem; min-height: 1.1rem; text-align: center; }
    .review-rating-active { color: #6b2d3c; font-weight: 700; }
    .review-field { display: block; min-width: 0; }
    .review-input { background: #fffdfa; border: 1px solid rgba(107, 45, 60, .18); border-radius: 1rem; color: #3b2a2a; display: block; font-size: .95rem; line-height: 1.5; margin-top: .5rem; outline: 0; padding: .85rem 1rem; transition: background .18s, border-color .18s, box-shadow .18s; width: 100%; }
    .review-input::placeholder { color: rgba(59, 42, 42, .4); }
    .review-input:hover { border-color: rgba(107, 45, 60, .32); }
    .review-input:focus { background: #fff; border-color: #c98b8b; box-shadow: 0 0 0 3px rgba(201, 139, 139, .2); }
    .review-textarea { min-height: 8.5rem; resize: vertical; }
    .review-error { color: #a33f42; display: block; font-size: .75rem; font-weight: 650; line-height: 1.4; margin-top: .45rem; }
    .review-submit { border: 0; min-height: 3.25rem; margin-top: .25rem; width: 100%; }
    .review-submit:disabled { cursor: wait; opacity: .68; transform: none; }
    .review-spinner { border: 2px solid rgba(255, 255, 255, .4); border-radius: 999px; border-top-color: #fff; height: 1rem; width: 1rem; animation: review-spin .7s linear infinite; }
    .review-success { align-items: flex-start; background: rgba(238, 248, 239, .9); border: 1px solid rgba(76, 128, 96, .2); border-radius: 1.25rem; display: flex; gap: .75rem; margin-top: 1.5rem; padding: 1rem; }
    .review-success-icon { align-items: center; background: #4c8060; border-radius: 999px; color: #fff; display: grid; flex: 0 0 auto; font-weight: 800; height: 1.5rem; justify-content: center; width: 1.5rem; }
    @keyframes review-spin { to { transform: rotate(360deg); } }
    @media (min-width: 640px) { .review-modal-backdrop { padding: 1.5rem; } .review-modal-panel { padding: 2rem; } }
</style>
@endpush
