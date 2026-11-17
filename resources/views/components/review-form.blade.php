<div x-data="{ reviewOpen: true, rating: @js((int) old('rating', 0)) }">
    <div x-show="reviewOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[70] grid place-items-center bg-ink/55 p-4 sm:p-6" @keydown.escape.window="reviewOpen = false" @click.self="reviewOpen = false" role="dialog" aria-modal="true" aria-labelledby="confirmed-review-title">
        <div x-show="reviewOpen" x-transition class="w-full max-w-md rounded-[2rem] bg-crema p-6 shadow-2xl sm:p-8" @click.stop>
            <div class="flex items-start justify-between gap-4"><div><p class="eyebrow">Tu opinión</p><h2 id="confirmed-review-title" class="mt-2 font-display text-3xl text-bordo">¿Qué te pareció?</h2></div><button type="button" @click="reviewOpen = false" class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blush text-xl leading-none text-bordo" aria-label="Cerrar valoración">&times;</button></div>
            @if (session('review_status'))
                <div class="mt-6 rounded-2xl bg-[#EEF8EF] p-4 text-sm leading-6 text-[#315D38]" role="status"><p class="font-bold">¡Gracias por tu valoración!</p><p class="mt-1">{{ session('review_status') }}</p></div><button type="button" @click="reviewOpen = false" class="btn-primary mt-6 w-full">Continuar</button>
            @else
                <p class="mt-3 text-sm leading-6 text-ink/60">Elegí tus estrellas y contanos brevemente cómo fue tu experiencia.</p>
                @if ($errors->any())<p class="mt-4 rounded-xl bg-[#FFF1EE] px-3 py-2 text-xs font-semibold leading-5 text-[#8A3C2E]" role="alert">{{ $errors->first() }}</p>@endif
                <form action="{{ route('reviews.store') }}" method="POST" class="mt-6">
                    @csrf
                    <fieldset><legend class="form-label">Tu valoración</legend><input type="hidden" name="rating" :value="rating"><div class="mt-3 flex gap-2" role="radiogroup" aria-label="Elegir cantidad de estrellas">@for ($star = 1; $star <= 5; $star++)<button type="button" @click="rating = {{ $star }}" :aria-checked="(rating === {{ $star }}).toString()" :class="rating >= {{ $star }} ? 'bg-caramelo text-white' : 'bg-white text-caramelo'" class="grid h-11 w-11 place-items-center rounded-full border border-caramelo/25 text-xl transition hover:bg-caramelo hover:text-white" role="radio" aria-label="{{ $star }} {{ $star === 1 ? 'estrella' : 'estrellas' }}">★</button>@endfor</div></fieldset>
                    <div class="mt-5 grid gap-4"><label class="block"><span class="form-label">Tu nombre</span><input name="name" value="{{ old('name') }}" required maxlength="80" class="form-input" placeholder="¿Cómo te llamás?"></label><label class="block"><span class="form-label">Comentario</span><textarea name="comment" required minlength="10" maxlength="600" rows="4" class="form-input resize-y" placeholder="Contanos qué te pareció...">{{ old('comment') }}</textarea></label></div>
                    <button type="submit" class="btn-primary mt-6 w-full">Enviar valoración <span aria-hidden="true">→</span></button>
                </form>
            @endif
        </div>
    </div>
</div>
