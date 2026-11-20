@extends('layouts.delicias')

@section('title', 'Opiniones de clientes | Delicias SN')
@section('description', 'Leé las opiniones de quienes encargaron tortas y pastelería artesanal en Delicias SN, San Nicolás de los Arroyos.')
@section('canonical', delicias_canonical_url('/opiniones'))

@section('content')
    <div class="pt-20">
        <div class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 lg:px-8">
        </div>
        <x-reviews :reviews="$reviews" :stats="$stats" />
    </div>
@endsection
