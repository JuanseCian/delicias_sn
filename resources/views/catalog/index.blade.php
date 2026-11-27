@extends('layouts.delicias')

@php
    $catalogUrl = delicias_canonical_url(route('catalog.index', [], false));
    $catalogSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        '@id' => $catalogUrl.'#itemlist',
        'name' => 'Productos de Delicias SN',
        'url' => $catalogUrl,
        'numberOfItems' => $products->count(),
        'itemListElement' => $products->values()->map(fn ($product, int $index): array => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $product->name,
            'url' => delicias_canonical_url(route('catalog.products.show', ['product' => $product->slug], false)),
        ])->all(),
    ];
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => delicias_canonical_url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Productos', 'item' => $catalogUrl],
        ],
    ];
@endphp

@section('title', 'Productos artesanales | Delicias SN')
@section('description', 'Conocé todos los productos de pastelería artesanal de Delicias SN en San Nicolás de los Arroyos. Consultá tamaños, precios y opciones.')
@section('canonical', $catalogUrl)

@push('structured-data')
    @include('partials.structured-data', ['schemas' => [$catalogSchema, $breadcrumbSchema]])
@endpush

@section('content')
    @php
        $categoryGroups = $products
            ->groupBy('category_id')
            ->sortBy(fn ($categoryProducts) => $categoryProducts->first()->category->order)
            ->values();
    @endphp

    <div>
        <section class="catalog-hero relative isolate overflow-hidden px-4 pb-10 pt-36 sm:px-6 sm:pb-14 sm:pt-44 lg:px-8">
            <div class="pointer-events-none absolute -right-32 top-20 -z-10 h-96 w-96 rounded-full bg-blush/55 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -left-24 bottom-0 -z-10 h-72 w-72 rounded-full bg-caramelo/10 blur-3xl" aria-hidden="true"></div>

            <div class="mx-auto max-w-7xl">
                <div class="max-w-2xl">
                    <p class="section-kicker">Catálogo completo</p>
                    <h1 class="mt-4 font-display text-5xl leading-[1.02] tracking-[-0.04em] text-bordo sm:text-6xl lg:text-7xl">Todos los <span class="font-script text-6xl font-normal text-rosa-viejo sm:text-7xl lg:text-8xl">productos</span></h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-ink/65">Recorré nuestros favoritos por categoría, elegí el tamaño ideal y reservá tu fecha con tiempo. Todo se hornea a pedido.</p>
                </div>

                <nav class="mt-9 flex flex-wrap gap-2.5" aria-label="Categorías de productos">
                    @foreach ($categoryGroups as $categoryProducts)
                        @php($category = $categoryProducts->first()->category)
                        <a href="#categoria-{{ $category->slug }}" class="group inline-flex items-center rounded-full border border-bordo/10 bg-white/80 px-5 py-2.5 text-sm font-semibold text-bordo shadow-card backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:border-bordo hover:bg-bordo hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-bordo focus-visible:ring-offset-2 focus-visible:ring-offset-crema">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </section>

        <div class="px-4 pb-24 pt-10 sm:px-6 sm:pt-14 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="space-y-20">
                    @foreach ($categoryGroups as $categoryProducts)
                        @php($category = $categoryProducts->first()->category)
                        <section id="categoria-{{ $category->slug }}" class="scroll-mt-28" aria-labelledby="titulo-{{ $category->slug }}">
                            <div class="border-b border-bordo/10 pb-5">
                                <div>
                                    <h2 id="titulo-{{ $category->slug }}" class="font-display text-2xl leading-tight text-bordo sm:text-3xl">{{ $category->name }}</h2>
                                </div>
                            </div>

                            <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3 xl:grid-cols-4">
                                @foreach ($categoryProducts as $product)
                                    @php($sizeLabels = $product->sizes->sortBy('price')->pluck('label'))
                                    <a href="{{ route('catalog.products.show', ['product' => $product->slug]) }}" class="catalog-card group flex h-full flex-col overflow-hidden rounded-3xl bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-bordo sm:rounded-4xl">
                                        <div class="product-image-frame relative aspect-square">
                                            @if ($product->image)
                                                <img src="{{ product_image_url($product->image) }}" alt="{{ $product->name }} en {{ $category->name }}" width="1000" height="1000" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                                            @else
                                                <span class="px-3 text-center font-script text-3xl text-bordo/60">{{ $product->name }}</span>
                                            @endif
                                            @if ($product->is_featured)
                                                <span class="absolute right-2 top-2 rounded-full bg-caramelo px-2 py-1.5 text-[8px] font-bold uppercase tracking-[0.08em] text-white shadow-card sm:right-3 sm:top-3 sm:px-3 sm:py-2 sm:text-[10px]">Destacado</span>
                                            @endif
                                            <span class="absolute bottom-3 right-3 hidden h-11 w-11 translate-y-2 place-items-center rounded-full bg-white text-bordo opacity-0 shadow-card transition duration-300 group-hover:translate-y-0 group-hover:opacity-100 sm:grid" aria-hidden="true">→</span>
                                        </div>
                                        <div class="flex flex-1 flex-col p-3.5 sm:p-5">
                                            <h3 class="line-clamp-2 font-display text-lg leading-tight text-bordo sm:text-xl">{{ $product->name }}</h3>
                                            @if ($product->short_description)
                                                <p class="mt-1.5 line-clamp-2 text-xs leading-5 text-ink/60 sm:text-sm sm:leading-6">{{ $product->short_description }}</p>
                                            @endif
                                            @if ($sizeLabels->isNotEmpty())
                                                <div class="mt-3 hidden flex-wrap gap-1.5 sm:flex">
                                                    @foreach ($sizeLabels->take(2) as $sizeLabel)
                                                        <span class="rounded-full border border-bordo/10 bg-crema px-2.5 py-1 text-[11px] font-semibold text-ink/60">{{ $sizeLabel }}</span>
                                                    @endforeach
                                                    @if ($sizeLabels->count() > 2)
                                                        <span class="rounded-full px-1.5 py-1 text-[11px] font-semibold text-ink/45">+{{ $sizeLabels->count() - 2 }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-auto flex items-end justify-between gap-3 pt-4">
                                                <div>
                                                    <p class="text-[9px] font-bold uppercase tracking-[0.1em] text-ink/45">Desde</p>
                                                    <p class="font-display text-lg text-bordo sm:text-xl">{{ format_price($product->starting_price) }}</p>
                                                </div>
                                                <span class="rounded-full bg-blush px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-bordo transition group-hover:bg-bordo group-hover:text-white sm:text-xs">Ver</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <x-footer />
@endsection
