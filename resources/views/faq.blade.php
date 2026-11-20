@extends('layouts.delicias')

@section('title', 'Preguntas frecuentes sobre pedidos | Delicias SN')
@section('description', 'Consultá cómo reservar, qué anticipación necesitás, las opciones de envío y retiro en San Nicolás y cómo conservar tus productos de Delicias SN.')
@section('canonical', delicias_canonical_url('/preguntas-frecuentes'))

@push('structured-data')
    @php
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->map(fn (array $faq): array => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ])->values()->all(),
        ];
    @endphp
    @include('partials.structured-data', ['schemas' => [$faqSchema]])
@endpush

@section('content')
    <x-faq :faqs="$faqs" />
    <x-footer />
@endsection
