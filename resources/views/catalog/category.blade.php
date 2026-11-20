@extends('layouts.delicias')

@section('title', $category->name.' · Delicias Sn')

@push('head')
    <link rel="canonical" href="{{ rtrim(config('delicias.canonical_url'), '/') }}{{ route('catalog.categories.show', ['category' => $category->slug], false) }}">
@endpush

@section('content')
    <section class="px-4 pb-20 pt-36 sm:px-6 sm:pb-28 sm:pt-44 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <a href="{{ route('home') }}#catalogo" class="text-sm font-semibold text-ink/55 transition hover:text-bordo">← Volver al catálogo</a>
            <div class="mt-10 max-w-2xl">
                <p class="section-kicker">Catálogo</p>
                <h1 class="mt-4 font-display text-5xl leading-tight tracking-[-0.04em] text-bordo sm:text-6xl">{{ $category->name }}</h1>
                <p class="mt-4 text-base leading-7 text-ink/65">Elegí tu favorito y consultá las opciones disponibles para reservar tu fecha.</p>
            </div>

            <div class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
                @foreach ($products as $product)
                    <a href="{{ route('catalog.products.show', ['product' => $product->slug]) }}" class="catalog-card group flex h-full flex-col overflow-hidden rounded-3xl bg-white sm:rounded-4xl">
                        <div class="product-image-frame aspect-square sm:aspect-[1.15]">
                            @if ($product->image)
                                <img src="{{ product_image_url($product->image) }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                            @else
                                <span class="px-3 text-center font-script text-3xl text-bordo/60">{{ $product->name }}</span>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-3 sm:p-6">
                            <h2 class="line-clamp-2 min-h-[2.75rem] font-display text-lg leading-tight text-bordo sm:min-h-[3.75rem] sm:text-2xl">{{ $product->name }}</h2>
                            <div class="mt-auto flex items-end justify-between gap-3 border-t border-bordo/10 pt-4">
                                <div><p class="text-[9px] font-bold uppercase tracking-[0.1em] text-ink/45">Desde</p><p class="mt-1 font-display text-lg text-bordo sm:text-xl">{{ format_price($product->starting_price) }}</p></div>
                                <span class="text-xs font-bold uppercase tracking-[0.08em] text-bordo">Ver</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    <x-footer />
@endsection
