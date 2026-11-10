@extends('layouts.private')

@section('title', 'Catálogo · Delicias Sn')

@section('content')
<main class="admin-products min-h-screen bg-crema px-4 py-6 text-ink sm:px-6 lg:px-10">
    <div class="mx-auto max-w-7xl">
        <header class="admin-surface flex flex-col justify-between gap-5 rounded-[1.75rem] border p-6 sm:flex-row sm:items-end sm:p-8">
            <div><p class="eyebrow">Administración de catálogo</p><h1 class="admin-heading mt-2 text-3xl tracking-tight sm:text-4xl">Productos</h1><p class="admin-muted mt-2 max-w-2xl text-sm leading-6">Creá y editá lo que aparece en la landing. Cada tamaño, cantidad, color e imagen se guarda en la base de datos.</p></div>
            <div class="flex gap-2"><a href="{{ route('dashboard') }}" class="admin-button-soft rounded-xl px-4 py-3 text-sm font-semibold">Panel</a><a href="{{ route('private.products.create') }}" class="admin-button rounded-xl px-5 py-3 text-sm font-bold">+ Nuevo producto</a></div>
        </header>

        <div class="mt-6 grid gap-4 sm:grid-cols-3"><div class="admin-surface rounded-2xl border p-5"><p class="eyebrow">Productos</p><p class="admin-heading mt-2 text-3xl font-bold">{{ $products->count() }}</p></div><div class="admin-surface rounded-2xl border p-5"><p class="eyebrow">Con imagen</p><p class="mt-2 text-3xl font-bold text-[#4C8060]">{{ $products->whereNotNull('image')->count() }}</p></div><div class="admin-surface rounded-2xl border p-5"><p class="eyebrow">Sin imagen</p><p class="mt-2 text-3xl font-bold text-caramelo">{{ $products->whereNull('image')->count() }}</p></div></div>

        <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <article class="admin-surface overflow-hidden rounded-[1.5rem] border">
                    <div class="flex aspect-[1.35] items-center justify-center overflow-hidden bg-blush/35">
                        @if ($product->image)<img src="{{ product_image_url($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">@else<span class="text-center text-bordo/55"><span class="block text-4xl">○</span><span class="mt-1 block text-xs font-semibold">Sin imagen cargada</span></span>@endif
                    </div>
                    <div class="p-5"><div class="flex items-start justify-between gap-3"><div><p class="text-xs font-bold tracking-[0.1em] text-[#64747A] uppercase">{{ $product->category->name }}</p><h2 class="mt-1 text-xl font-bold text-[#263238]">{{ $product->name }}</h2></div><span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $product->active ? 'bg-[#EEF8EF] text-[#315D38]' : 'bg-[#F0F1F1] text-[#64747A]' }}">{{ $product->active ? 'Activo' : 'Oculto' }}</span></div><p class="mt-3 line-clamp-2 text-sm leading-5 text-[#263238]/60">{{ $product->short_description }}</p><div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold text-[#64747A]"><span class="rounded-lg bg-[#F5F1EC] px-2.5 py-1.5">{{ $product->sizes->count() }} tamaños</span><span class="rounded-lg bg-[#F5F1EC] px-2.5 py-1.5">{{ $product->colors->count() }} colores</span><span class="rounded-lg bg-[#F5F1EC] px-2.5 py-1.5">{{ $product->sizes->sum('quantity') }} unidades</span></div><div class="mt-5 flex gap-2 border-t border-[#263238]/10 pt-4"><a href="{{ route('private.products.edit', $product) }}" class="flex-1 rounded-xl bg-[#E9EFF0] px-3 py-2.5 text-center text-sm font-bold text-[#263238] transition hover:bg-[#D8E2E4]">Editar producto</a><form action="{{ route('private.products.destroy', $product) }}" method="POST" onsubmit="return confirm('¿Querés eliminar este producto?')">@csrf @method('DELETE')<button type="submit" class="rounded-xl px-3 py-2.5 text-sm font-semibold text-[#A85C22] transition hover:bg-[#FFF1EE]" aria-label="Eliminar {{ $product->name }}">Eliminar</button></form></div></div>
                </article>
            @endforeach
        </div>
    </div>
</main>
@endsection
