@extends('layouts.private')

@section('title', 'Valoraciones · Delicias Sn')

@section('content')
<main class="min-h-screen bg-crema px-4 py-6 text-ink sm:px-6 lg:px-10">
    <div class="mx-auto max-w-5xl">
        <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#64747A] hover:text-[#263238]">← Volver al panel</a><p class="mt-5 text-xs font-bold tracking-[0.14em] text-[#64747A] uppercase">Experiencias</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-[#263238] sm:text-4xl">Valoraciones</h1><p class="mt-2 max-w-2xl text-sm leading-6 text-[#263238]/60">Revisá los comentarios antes de publicarlos en la página.</p></div><a href="{{ route('home') }}#opiniones" target="_blank" rel="noreferrer" class="rounded-xl bg-[#E9EFF0] px-4 py-3 text-center text-sm font-bold text-[#263238]">Ver sección pública</a></header>
        @if (session('status'))<p class="mt-6 rounded-xl bg-[#EEF8EF] px-4 py-3 text-sm font-semibold text-[#315D38]" role="status">{{ session('status') }}</p>@endif
        <div class="mt-6 grid gap-4">
            @forelse ($reviews as $review)
                <article class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-5 shadow-[0_8px_24px_rgba(38,50,50,0.06)] sm:p-6"><div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start"><div><div class="flex gap-0.5 text-xl text-[#C68B59]" aria-label="{{ $review->rating }} de 5 estrellas">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $review->rating ? '' : 'opacity-25' }}">★</span>@endfor</div><p class="mt-3 text-xs font-bold uppercase tracking-[0.12em] text-[#6B2D3C]">{{ $review->name }} · {{ $review->created_at->format('d/m/Y H:i') }}</p></div><span class="rounded-full px-3 py-2 text-[10px] font-bold uppercase tracking-[0.1em] {{ $review->approved ? 'bg-[#EEF8EF] text-[#315D38]' : 'bg-[#FFF1EE] text-[#8A3C2E]' }}">{{ $review->approved ? 'Publicada' : 'Pendiente' }}</span></div><p class="mt-4 text-sm leading-7 text-[#263238]/70">{{ $review->comment }}</p><form action="{{ route('private.reviews.update', $review) }}" method="POST" class="mt-5">@csrf @method('PATCH')<button type="submit" class="rounded-xl bg-[#E9EFF0] px-4 py-2.5 text-sm font-bold text-[#263238]">{{ $review->approved ? 'Ocultar valoración' : 'Publicar valoración' }}</button></form></article>
            @empty
                <div class="rounded-[1.5rem] border border-dashed border-[#263238]/15 bg-white p-8 text-center text-sm text-[#263238]/55">Todavía no recibiste valoraciones.</div>
            @endforelse
        </div>
    </div>
</main>
@endsection
