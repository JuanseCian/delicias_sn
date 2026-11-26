@extends('layouts.delicias')

@section('title', 'Página no encontrada · Delicias SN')
@section('description', 'La página que buscás no está disponible en Delicias SN.')
@section('robots', 'noindex, nofollow, noarchive')
@section('without_seasonal_theme', true)

@php
    $suggestions = [
        ['label' => 'Productos', 'hint' => 'Tortas, cookies y boxes', 'href' => route('home').'#catalogo'],
        ['label' => 'Personalizadas', 'hint' => 'Diseñá tu torta', 'href' => route('personalized')],
        ['label' => 'Opiniones', 'hint' => 'Lo que dicen nuestros clientes', 'href' => route('reviews.public')],
        ['label' => 'Preguntas', 'hint' => 'Envíos, pagos y plazos', 'href' => route('faq')],
    ];
@endphp

@section('content')
<section class="error-page relative isolate overflow-hidden px-4 pb-20 pt-32 sm:px-6 sm:pb-28 sm:pt-40 lg:px-8">
    <div class="pointer-events-none absolute -left-40 top-24 -z-10 h-80 w-80 rounded-full bg-blush/60 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -right-32 bottom-10 -z-10 h-96 w-96 rounded-full bg-caramelo/15 blur-3xl" aria-hidden="true"></div>
    <div class="error-dots pointer-events-none absolute inset-0 -z-10" aria-hidden="true"></div>

    <div class="mx-auto flex max-w-3xl flex-col items-center text-center">
        <p class="eyebrow !text-bordo/60">Error 404 · Página no encontrada</p>

        <p class="mt-6 flex items-center justify-center gap-[0.04em] font-poster text-[clamp(7rem,24vw,13rem)] leading-none tracking-[-0.04em] text-bordo" aria-label="404">
            <span aria-hidden="true">4</span>
            <svg class="error-cake mx-[0.02em] h-[0.82em] w-[0.82em] shrink-0" viewBox="-12 -12 224 224" aria-hidden="true">
                <circle cx="100" cy="104" r="96" fill="#6b2d3c" opacity=".08" />
                <circle cx="100" cy="100" r="96" fill="#fff" stroke="#eadfd7" stroke-width="2" />
                <circle cx="100" cy="100" r="84" fill="none" stroke="#eadfd7" stroke-width="1.5" stroke-dasharray="2 6" />

                <circle cx="100" cy="100" r="72" fill="#f4d9d5" />
                <circle cx="100" cy="100" r="72" fill="none" stroke="#c98b8b" stroke-width="5" stroke-dasharray="1 11" stroke-linecap="round" />
                <circle cx="100" cy="100" r="46" fill="none" stroke="#fff" stroke-width="3" opacity=".75" />
                <circle cx="70" cy="70" r="7" fill="#6b2d3c" />
                <circle cx="56" cy="112" r="7" fill="#6b2d3c" />
                <circle cx="92" cy="150" r="7" fill="#6b2d3c" />
                <circle cx="138" cy="132" r="7" fill="#6b2d3c" />
                <circle cx="100" cy="100" r="10" fill="#c68b59" />

                <path d="M100 100 L124.6 32.3 A72 72 0 0 1 165.2 69.6 Z" fill="#fff" />
                <path d="M124.6 32.3 L100 100 L165.2 69.6" fill="none" stroke="#c68b59" stroke-width="6" stroke-linejoin="round" stroke-linecap="round" />

                <g class="error-cake-slice">
                    <g transform="translate(17 -18)">
                        <path d="M100 100 L124.6 32.3 A72 72 0 0 1 165.2 69.6 Z" fill="#f4d9d5" stroke="#c68b59" stroke-width="5" stroke-linejoin="round" />
                        <path d="M124.6 32.3 A72 72 0 0 1 165.2 69.6" fill="none" stroke="#c98b8b" stroke-width="5" stroke-dasharray="1 11" stroke-linecap="round" />
                        <circle cx="136" cy="58" r="6" fill="#6b2d3c" />
                    </g>
                </g>

                <circle cx="172" cy="104" r="3" fill="#c68b59" />
                <circle cx="182" cy="92" r="2" fill="#c68b59" />
                <circle cx="164" cy="118" r="2" fill="#c98b8b" />
            </svg>
            <span aria-hidden="true">4</span>
        </p>

        <h1 class="mt-6 font-display text-4xl leading-tight tracking-[-0.04em] text-bordo sm:text-5xl">
            Alguien se llevó <span class="font-script text-5xl font-normal text-rosa-viejo sm:text-6xl">esta porción.</span>
        </h1>
        <p class="mt-5 max-w-lg text-base leading-7 text-ink/65">
            La página que buscás no existe o cambió de lugar. No te preocupes: el resto de la torta sigue intacta.
        </p>

        <div class="mt-9 flex w-full flex-col justify-center gap-3 sm:w-auto sm:flex-row">
            <a href="{{ route('home') }}" class="btn-primary">Volver al inicio <span aria-hidden="true">→</span></a>
            <a href="{{ route('home') }}#catalogo" class="btn-secondary">Ver el mostrador <span aria-hidden="true">↗</span></a>
        </div>

        <div class="mt-16 w-full">
            <div class="flex items-center gap-4">
                <span class="h-px flex-1 bg-bordo/10" aria-hidden="true"></span>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-bordo/50">Quizás buscabas</p>
                <span class="h-px flex-1 bg-bordo/10" aria-hidden="true"></span>
            </div>

            <nav class="mt-6 grid gap-3 sm:grid-cols-2" aria-label="Secciones sugeridas">
                @foreach ($suggestions as $suggestion)
                    <a href="{{ $suggestion['href'] }}" class="group flex items-center justify-between gap-4 rounded-2xl border border-bordo/10 bg-white/70 px-5 py-4 text-left shadow-card transition duration-300 hover:-translate-y-0.5 hover:border-rosa-viejo/50 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-bordo">
                        <span>
                            <span class="block font-display text-lg text-bordo">{{ $suggestion['label'] }}</span>
                            <span class="block text-sm text-ink/55">{{ $suggestion['hint'] }}</span>
                        </span>
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blush text-bordo transition group-hover:bg-bordo group-hover:text-white" aria-hidden="true">→</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</section>
@endsection

@push('head')
<style>
    .error-dots {
        background-image: radial-gradient(rgba(107, 45, 60, .07) 1.5px, transparent 1.5px);
        background-size: 26px 26px;
        mask-image: radial-gradient(ellipse at center, #000 20%, transparent 70%);
    }

    .error-cake {
        filter: drop-shadow(0 18px 24px rgba(107, 45, 60, .14));
    }

    .error-cake-slice {
        animation: error-slice-float 4.5s ease-in-out infinite;
        transform-origin: 140px 60px;
    }

    @keyframes error-slice-float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        50% { transform: translate(3px, -5px) rotate(4deg); }
    }

    @media (prefers-reduced-motion: reduce) {
        .error-cake-slice { animation: none; }
    }
</style>
@endpush
