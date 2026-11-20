@extends('layouts.delicias')

@section('title', '¡Reserva recibida! · Delicias Sn')
@section('robots', 'noindex, nofollow, noarchive')

@section('content')
@php
    $transferData = [
        'alias' => config('delicias.alias'),
        'cbu' => config('delicias.cbu'),
        'holder' => config('delicias.account_holder'),
        'cuil' => config('delicias.cuil'),
    ];
    $ticketDetails = [
        ['label' => 'Entrega', 'value' => ucfirst($order->delivery_date->locale('es')->translatedFormat('l j \d\e F')).' · '.$order->time_slot, 'icon' => 'calendar'],
        ['label' => 'Modalidad', 'value' => $order->delivery_type === 'envio' ? 'Envío a domicilio' : 'Retiro por Jujuy 422', 'icon' => 'delivery'],
    ];
@endphp
<section class="px-4 pb-20 pt-36 sm:px-6 sm:pb-28 sm:pt-44 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <div class="text-center">
            <p class="section-kicker">Comprobante de reserva</p>
            <p class="mx-auto mt-4 max-w-lg text-base leading-7 text-ink/65">Tu pedido quedó reservado. Guardá este ticket y escribinos por WhatsApp para coordinar los detalles.</p>
        </div>

        <div class="mt-10 flex min-h-[34rem] justify-center" data-ticket-confirmation
             data-ticket-id="{{ $order->code }}"
             data-amount="{{ $order->subtotal }}"
             data-date="{{ $order->created_at->toIso8601String() }}"
             data-card-holder="{{ $order->customer_name }}"
             data-whatsapp-url="{{ $whatsappUrl }}"
             data-deposit="{{ $order->deposit }}"
             data-transfer='@json($transferData)'
             data-logo="{{ asset('img/icono_delicias.png') }}"
             data-details='@json($ticketDetails)'>
        </div>
    </div>
</section>

<x-review-form />
@endsection

@push('head')
    @viteReactRefresh
@endpush

@push('scripts')
    @vite('resources/js/ticket-confirmation-entry.tsx')
@endpush
