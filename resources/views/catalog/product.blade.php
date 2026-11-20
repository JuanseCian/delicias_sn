@extends('layouts.delicias')

@section('title', $product->name.' · Delicias Sn')

@push('head')
    <link rel="canonical" href="{{ rtrim(config('delicias.canonical_url'), '/') }}{{ route('catalog.products.show', ['product' => $product->slug], false) }}">
@endpush

@section('content')
    <section class="px-4 pb-20 pt-36 sm:px-6 sm:pb-28 sm:pt-44 lg:px-8">
        <div class="mx-auto max-w-6xl">
            <a href="{{ route('home') }}#catalogo" class="text-sm font-semibold text-ink/55 transition hover:text-bordo">← Volver al catálogo</a>
            <article class="mt-8 grid overflow-hidden rounded-[2rem] bg-white shadow-card lg:grid-cols-2">
                <div class="product-modal-image min-h-72 lg:min-h-[34rem]">
                    @if ($product->image)
                        <img src="{{ product_image_url($product->image) }}" alt="{{ $product->name }}" class="h-full min-h-72 w-full object-cover lg:min-h-[34rem]">
                    @else
                        <div class="grid h-full min-h-72 place-items-center p-8 text-center lg:min-h-[34rem]"><span class="font-script text-6xl text-bordo/65">{{ $product->name }}</span></div>
                    @endif
                </div>
                <div class="p-6 sm:p-10">
                    <p class="eyebrow">{{ $product->category->name }}</p>
                    <h1 class="mt-3 font-display text-4xl leading-tight text-bordo sm:text-5xl">{{ $product->name }}</h1>
                    <p class="mt-5 text-base leading-7 text-ink/70">{{ $product->description }}</p>

                    <div class="mt-8">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-bordo">Tamaños disponibles</p>
                        <div class="mt-3 space-y-2">
                            @foreach ($product->sizes as $size)
                                <div class="flex items-center justify-between rounded-2xl border border-bordo/10 bg-crema px-4 py-3"><span class="text-sm text-ink/70">{{ $size->label }}</span><strong class="text-sm text-bordo">{{ format_price($size->price) }}</strong></div>
                            @endforeach
                        </div>
                    </div>

                    @if ($product->ingredients->isNotEmpty())
                        <div class="mt-8 border-t border-bordo/10 pt-6">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-bordo">Ingredientes</p>
                            <p class="mt-2 text-sm leading-6 text-ink/65">{{ $product->ingredients->pluck('name')->join(' · ') }}</p>
                        </div>
                    @endif

                    <a href="{{ route('home') }}#catalogo" class="btn-primary mt-8 w-full">Elegir opciones y reservar <span aria-hidden="true">→</span></a>
                </div>
            </article>
        </div>
    </section>
    <x-footer />
@endsection
