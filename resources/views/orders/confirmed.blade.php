@extends('layouts.delicias')

@section('title', '¡Reserva recibida! · Delicias Sn')

@section('content')
<section class="px-4 pb-20 pt-36 sm:px-6 sm:pb-28 sm:pt-44 lg:px-8">
    <div class="mx-auto max-w-2xl text-center">
        <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-blush text-4xl text-bordo">✓</span>
        <p class="section-kicker mt-8">Reserva recibida</p>
        <h1 class="mt-4 font-display text-5xl tracking-[-0.04em] text-bordo sm:text-6xl">¡Qué <span class="font-script text-6xl font-normal text-rosa-viejo">alegría!</span></h1>
        <p class="mx-auto mt-5 max-w-lg text-base leading-7 text-ink/65">Tu pedido quedó reservado. Escribinos por WhatsApp para coordinar los detalles.</p>

        <div class="mt-9 rounded-[2rem] bg-white p-6 text-left shadow-card sm:p-9">
            <div class="flex flex-col justify-between gap-2 border-b border-bordo/10 pb-5 sm:flex-row sm:items-center">
                <div><p class="eyebrow">Número de pedido</p><p class="mt-2 font-display text-2xl text-bordo">{{ $order->code }}</p></div>
                <span class="rounded-full bg-blush px-3 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-bordo">Reservado</span>
            </div>
            <div class="mt-6 space-y-3 text-sm text-ink/65">
                <div class="flex justify-between gap-4"><span>Nombre</span><strong class="text-right text-ink">{{ $order->customer_name }}</strong></div>
                <div class="flex justify-between gap-4"><span>Entrega</span><strong class="text-right text-ink">{{ $order->delivery_date->locale('es')->translatedFormat('l j \d\e F') }} · {{ $order->time_slot }}</strong></div>
                <div class="flex justify-between gap-4"><span>Modalidad</span><strong class="text-right text-ink">{{ $order->delivery_type === 'envio' ? 'Envío a domicilio' : 'Retiro por Catamarca 523' }}</strong></div>
            </div>
            <div class="mt-6 border-t border-bordo/10 pt-5">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-bordo">Detalle del pedido</p>
                <div class="mt-3 space-y-2 text-sm text-ink/65">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between gap-3"><span>{{ $item->product->name }} · {{ $item->size->label }}{{ $item->color ? ' · '.$item->color->name : '' }} · x{{ $item->quantity }}</span><strong class="text-ink">{{ format_price($item->unit_price * $item->quantity) }}</strong></div>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-between border-t border-bordo/10 pt-4 font-bold text-bordo"><span>Total</span><span>{{ format_price($order->subtotal) }}</span></div>
            </div>
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn-primary mt-7 w-full">Escribir por WhatsApp <span aria-hidden="true">↗</span></a>
        </div>
    </div>
</section>

<x-review-form />
@endsection
