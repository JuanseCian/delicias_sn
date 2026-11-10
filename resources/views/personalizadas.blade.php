@extends('layouts.delicias')

@section('title', 'Personalizá tu torta · Delicias Sn')

@section('content')
<main class="pt-20">
    <x-custom-cakes :products="$customProducts" :customizer-options="$customizerOptions" />
</main>
@endsection
