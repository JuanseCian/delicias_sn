@extends('layouts.delicias')

@section('title', 'Delicias Sn · Endulza tus momentos')

@section('content')
    <x-hero />
    <x-benefits />
    @if ($featured->isNotEmpty())
        <x-featured :featured="$featured" />
    @endif
    <x-catalog :categories="$categories" :products="$products" />
    <x-steps />
    <x-footer />
@endsection
