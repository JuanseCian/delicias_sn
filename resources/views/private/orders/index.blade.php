@extends('layouts.private')

@section('title', 'Pedidos · Delicias Sn')

@php
    $statusLabels = [
        'pendiente' => 'Pendiente',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
    ];
    $statusStyles = [
        'pendiente' => 'bg-[#FFF1EE] text-[#8A3C2E]',
        'entregado' => 'bg-[#EEF8EF] text-[#315D38]',
        'cancelado' => 'bg-[#F0F1F1] text-[#64747A]',
    ];
@endphp

@section('content')
<main class="min-h-screen bg-crema px-4 py-6 text-ink sm:px-6 lg:px-10">
    <div class="mx-auto max-w-6xl">
        <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#64747A] hover:text-[#263238]">← Volver al panel</a><p class="mt-5 text-xs font-bold tracking-[0.14em] text-[#64747A] uppercase">Gestión de pedidos</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-[#263238] sm:text-4xl">Pedidos</h1><p class="mt-2 max-w-2xl text-sm leading-6 text-[#263238]/60">Cada reserva entra como pendiente. Desde acá podés marcarla como entregada o cancelada.</p></div><a href="{{ route('home') }}" target="_blank" rel="noreferrer" class="rounded-xl bg-[#E9EFF0] px-4 py-3 text-center text-sm font-bold text-[#263238]">Ver página pública</a></header>
        @if (session('status'))<p class="mt-6 rounded-xl bg-[#EEF8EF] px-4 py-3 text-sm font-semibold text-[#315D38]" role="status">{{ session('status') }}</p>@endif

        <div class="mt-6 grid gap-4 sm:grid-cols-3"><div class="rounded-2xl border border-[#263238]/10 bg-white p-5"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#64747A]">Pendientes</p><p class="mt-2 text-3xl font-bold text-[#8A3C2E]">{{ $orders->where('status', 'pendiente')->count() }}</p></div><div class="rounded-2xl border border-[#263238]/10 bg-white p-5"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#64747A]">Entregados</p><p class="mt-2 text-3xl font-bold text-[#315D38]">{{ $orders->where('status', 'entregado')->count() }}</p></div><div class="rounded-2xl border border-[#263238]/10 bg-white p-5"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#64747A]">Total histórico</p><p class="mt-2 text-3xl font-bold text-[#263238]">{{ $orders->count() }}</p></div></div>

        <div class="mt-6 grid gap-5">
            @forelse ($orders as $order)
                <article class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-5 shadow-[0_8px_24px_rgba(38,50,50,0.06)] sm:p-6"><div class="flex flex-col justify-between gap-4 border-b border-[#263238]/10 pb-5 sm:flex-row sm:items-start"><div><div class="flex flex-wrap items-center gap-2"><h2 class="text-xl font-bold text-[#263238]">{{ $order->code }}</h2><span class="rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.1em] {{ $statusStyles[$order->status] ?? 'bg-[#F0F1F1] text-[#64747A]' }}">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span></div><p class="mt-2 text-sm text-[#263238]/60">{{ $order->customer_name }} · <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->phone) }}" target="_blank" rel="noreferrer" class="font-semibold text-[#6B2D3C] hover:underline">{{ $order->phone }}</a></p></div><div class="text-left sm:text-right"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#64747A]">Entrega</p><p class="mt-1 text-sm font-semibold text-[#263238]">{{ $order->delivery_date->locale('es')->translatedFormat('l j \d\e F') }}</p><p class="text-xs text-[#263238]/55">{{ $order->time_slot }} · {{ $order->delivery_type === 'envio' ? 'Envío' : 'Retiro' }}</p></div></div><div class="grid gap-5 py-5 lg:grid-cols-[1fr_auto] lg:items-start"><div class="space-y-2 text-sm text-[#263238]/70">@foreach ($order->items as $item)<div class="flex justify-between gap-4"><span>{{ $item->product->name }} · {{ $item->size->label }}{{ $item->color ? ' · '.$item->color->name : '' }} · x{{ $item->quantity }}@if($item->note)<small class="block text-xs text-[#263238]/50">Nota: {{ $item->note }}</small>@endif</span><strong class="whitespace-nowrap text-[#263238]">{{ format_price($item->unit_price * $item->quantity) }}</strong></div>@endforeach</div><div class="rounded-2xl bg-[#F5F1EC] p-4 text-left lg:min-w-48"><p class="text-xs uppercase tracking-[0.12em] text-[#64747A]">Total</p><p class="mt-1 text-xl font-bold text-[#6B2D3C]">{{ format_price($order->subtotal) }}</p><p class="mt-1 text-xs text-[#263238]/55">Seña: {{ format_price($order->deposit) }}</p></div></div>
                @if ($order->status === 'entregado')
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#263238]/10 py-4">
                        @if ($order->review)
                            <div class="flex items-center gap-2 text-sm text-[#263238]/70">
                                <span class="flex gap-0.5 text-[#C68B59]" aria-label="{{ $order->review->rating }} de 5 estrellas">@for ($star = 1; $star <= 5; $star++)<span class="{{ $star <= $order->review->rating ? '' : 'opacity-25' }}">★</span>@endfor</span>
                                <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $order->review->approved ? 'bg-[#EEF8EF] text-[#315D38]' : 'bg-[#FFF1EE] text-[#8A3C2E]' }}">{{ $order->review->approved ? 'Opinión publicada' : 'Opinión pendiente de aprobar' }}</span>
                            </div>
                        @else
                            <p class="text-sm text-[#263238]/60">Todavía no dejó una opinión.</p>
                            <a href="{{ $reviewInviteUrls[$order->id] }}" target="_blank" rel="noreferrer" class="rounded-xl bg-[#25D366] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#1ebe5d]">Pedir opinión por WhatsApp</a>
                        @endif
                    </div>
                @endif
                <div class="flex flex-col justify-between gap-3 border-t border-[#263238]/10 pt-4 sm:flex-row sm:items-center"><p class="text-xs text-[#263238]/50">{{ $order->notes ?: 'Sin notas adicionales' }}</p><form action="{{ route('private.orders.update', $order) }}" method="POST" class="flex flex-col gap-2 sm:flex-row sm:items-center">@csrf @method('PATCH')<label class="sr-only" for="status-{{ $order->id }}">Estado del pedido</label><select id="status-{{ $order->id }}" name="status" class="rounded-xl border border-[#263238]/15 bg-white px-3 py-2.5 text-sm font-semibold text-[#263238]"><option value="pendiente" @selected($order->status === 'pendiente')>Pendiente</option><option value="entregado" @selected($order->status === 'entregado')>Entregado</option><option value="cancelado" @selected($order->status === 'cancelado')>Cancelado</option></select><button type="submit" class="rounded-xl bg-[#263238] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#455A64]">Guardar estado</button></form></div></article>
            @empty
                <div class="rounded-[1.5rem] border border-dashed border-[#263238]/15 bg-white p-8 text-center text-sm text-[#263238]/55">Todavía no hay pedidos registrados.</div>
            @endforelse
        </div>
    </div>
</main>
@endsection
