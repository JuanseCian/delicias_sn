@extends('layouts.delicias')

@section('title', 'Dejá tu opinión · Delicias Sn')
@section('robots', 'noindex, nofollow, noarchive')

@section('content')
<section class="px-4 pb-20 pt-36 sm:px-6 sm:pb-28 sm:pt-44 lg:px-8">
    <div class="mx-auto max-w-xl text-center">
        <p class="section-kicker">Pedido {{ $order->code }}</p>

        @if ($canReview)
            <h1 class="mt-4 font-display text-4xl text-bordo sm:text-5xl">Contanos qué tal estuvo</h1>
            <p class="mt-4 text-base leading-7 text-ink/65">Tu opinión nos ayuda a seguir mejorando cada detalle.</p>
        @elseif ($order->review)
            <h1 class="mt-4 font-display text-4xl text-bordo sm:text-5xl">¡Ya dejaste tu opinión!</h1>
            <p class="mt-4 text-base leading-7 text-ink/65">Gracias por contarnos tu experiencia, {{ $order->customer_name }}. Esto es lo que compartiste:</p>
            <div class="mt-8 rounded-[1.75rem] border border-bordo/10 bg-white p-6 text-left shadow-card">
                <div class="flex gap-0.5 text-lg text-caramelo" aria-label="{{ $order->review->rating }} de 5 estrellas">
                    @for ($star = 1; $star <= 5; $star++)
                        <span class="{{ $star <= $order->review->rating ? '' : 'opacity-25' }}">★</span>
                    @endfor
                </div>
                @if ($order->review->comment)
                    <blockquote class="mt-4 text-sm leading-7 text-ink/75">“{{ $order->review->comment }}”</blockquote>
                @endif
                <p class="mt-3 text-xs {{ $order->review->approved ? 'text-[#4C8060]' : 'text-ink/45' }}">
                    {{ $order->review->approved ? 'Ya está publicada en nuestras opiniones.' : 'La estamos revisando antes de publicarla.' }}
                </p>
            </div>
        @else
            <h1 class="mt-4 font-display text-4xl text-bordo sm:text-5xl">Todavía no podés opinar</h1>
            <p class="mt-4 text-base leading-7 text-ink/65">
                @if ($order->status === \App\Models\Order::STATUS_CANCELLED)
                    Este pedido fue cancelado, así que no podemos recibir una opinión sobre él.
                @else
                    Tu pedido está {{ $order->status === \App\Models\Order::STATUS_PENDING ? 'en preparación' : 'en camino' }} todavía. En cuanto lo recibas te vamos a escribir para que nos cuentes qué te pareció.
                @endif
            </p>
        @endif

        <a href="{{ route('home') }}" class="mt-8 inline-flex text-sm font-bold text-bordo underline decoration-blush underline-offset-4 hover:text-rosa-viejo">← Volver al inicio</a>
    </div>
</section>

@if ($canReview)
    <x-review-form :order="$order" />
@endif
@endsection
