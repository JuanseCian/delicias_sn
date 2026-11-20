@extends('layouts.delicias')

@section('title', 'Tortas Personalizadas en San Nicolás | Delicias SN')
@section('description', 'Diseñá tu torta personalizada en Delicias SN. Explorá tamaños, coberturas, colores y decoraciones, y compartí tu idea para coordinar el pedido en San Nicolás.')
@section('canonical', delicias_canonical_url('/personalizadas'))
@section('og_image', delicias_absolute_url(product_image_url($customProducts->first()?->image)) ?: delicias_canonical_url('img/icono_delicias.png'))
@section('og_image_alt', 'Diseñador de tortas personalizadas de Delicias SN')

@section('content')
<main class="pt-20">
    <x-custom-cakes :products="$customProducts" :customizer-options="$customizerOptions" />
</main>
@endsection
