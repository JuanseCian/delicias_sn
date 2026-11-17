@extends('layouts.delicias')

@section('title', 'Opiniones · Delicias Sn')

@section('content')
    <div class="pt-20">
        <div class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="text-sm font-semibold text-ink/55 transition hover:text-bordo">← Volver a la página principal</a>
        </div>
        <x-reviews :reviews="$reviews" :stats="$stats" />
    </div>
@endsection
