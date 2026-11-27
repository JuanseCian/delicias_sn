@extends('layouts.delicias')

@section('title', 'Delicias SN | Tortas y Pastelería Artesanal en San Nicolás')
@section('description', 'Descubrí tortas, cupcakes, cookies, boxes y mesas dulces artesanales en San Nicolás de los Arroyos. Hacé tu pedido en Delicias SN y reservá tu fecha.')
@section('canonical', delicias_canonical_url('/'))
@section('og_image', delicias_absolute_url(product_image_url($featured->first()?->image)) ?: delicias_canonical_url('img/icono_delicias.png'))
@section('og_image_alt', 'Delicias SN, pastelería artesanal en San Nicolás')

@push('structured-data')
    @php
        $localBusinessSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'additionalType' => 'https://schema.org/Bakery',
            '@id' => delicias_canonical_url().'#localbusiness',
            'name' => 'Delicias SN',
            'url' => delicias_canonical_url(),
            'image' => delicias_canonical_url('img/icono_delicias.png'),
            'telephone' => config('delicias.whatsapp_display'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => config('delicias.address'),
                'addressLocality' => 'San Nicolás de los Arroyos',
                'addressRegion' => 'Buenos Aires',
                'addressCountry' => 'AR',
            ],
            'areaServed' => 'San Nicolás de los Arroyos',
            'sameAs' => ['https://instagram.com/'.ltrim(config('delicias.instagram'), '@')],
            'hasMap' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode(config('delicias.address').', San Nicolás de los Arroyos, Buenos Aires'),
        ];
    @endphp
    @include('partials.structured-data', ['schemas' => [$localBusinessSchema]])
@endpush

@section('content')
    <x-hero />
    <x-benefits />
    @if ($featured->isNotEmpty())
        <x-featured :featured="$featured" />
    @endif
    <x-most-ordered :rail-products="$railProducts" :categories="$categories" />
    <x-product-modal />
    <x-reviews :reviews="$reviews" :stats="$stats" />
    <x-steps />
    <x-footer />
@endsection
