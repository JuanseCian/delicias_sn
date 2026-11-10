@extends('layouts.delicias')

@section('title', 'Delicias Sn · Cada bocado, una celebración')

@section('content')
    <x-hero />
    <x-benefits />
    <x-catalog :categories="$categories" :products="$products" />
    @if ($featured)
        <x-featured :featured="$featured" />
    @endif
    <x-steps />
    <x-footer />
@endsection
