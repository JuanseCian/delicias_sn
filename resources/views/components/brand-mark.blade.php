@props(['size' => 'h-10 w-10'])

<span {{ $attributes->merge(['class' => 'brand-mark']) }}>
    <img src="{{ asset('img/icono_delicias.png') }}" alt="" class="brand-mark-logo {{ $size }}">
</span>
