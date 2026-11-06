@extends('layouts.private')

@section('title', 'Cargar imágenes · Delicias Sn')

@section('content')
<main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <header class="flex flex-col justify-between gap-5 border-b border-bordo/10 pb-8 sm:flex-row sm:items-end">
        <div>
            <p class="eyebrow">Gestión privada</p>
            <h1 class="mt-2 font-display text-4xl text-bordo sm:text-5xl">Imágenes de productos</h1>
            <p class="mt-3 max-w-xl text-sm leading-6 text-ink/60">Cargá las fotos reales de cada producto. Las imágenes se guardan en `storage/app/public/products` y se muestran automáticamente en la landing.</p>
        </div>
        <a href="{{ route('home') }}" class="btn-secondary self-start">Ver landing <span aria-hidden="true">↗</span></a>
    </header>

    @if (session('success'))
        <div class="mt-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($products as $product)
            <article class="overflow-hidden rounded-3xl bg-white shadow-card">
                <div class="flex aspect-[1.35] items-center justify-center overflow-hidden bg-gradient-to-br from-blush via-crema to-mist">
                    @if ($product->image)
                        <img src="{{ product_image_url($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="text-center text-bordo/65"><span class="font-script text-5xl">sin foto</span><p class="mt-1 text-[10px] font-bold uppercase tracking-[0.15em]">lista para cargar</p></div>
                    @endif
                </div>
                <div class="p-5">
                    <p class="eyebrow">{{ $product->category->name }}</p>
                    <h2 class="mt-2 font-display text-2xl text-bordo">{{ $product->name }}</h2>
                    <form action="{{ route('private.products.image.store', $product) }}" method="POST" enctype="multipart/form-data" class="mt-5">
                        @csrf
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.1em] text-bordo">Foto JPG, PNG o WEBP · máx. 5 MB</span>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required class="mt-2 block w-full cursor-pointer rounded-2xl border border-dashed border-bordo/20 bg-crema p-3 text-xs text-ink/60 file:mr-3 file:rounded-full file:border-0 file:bg-blush file:px-3 file:py-2 file:text-xs file:font-bold file:text-bordo">
                        </label>
                        <button type="submit" class="btn-primary mt-3 w-full">{{ $product->image ? 'Reemplazar imagen' : 'Cargar imagen' }}</button>
                    </form>
                    @if ($product->image)
                        <form action="{{ route('private.products.image.destroy', $product) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-full px-4 py-2 text-xs font-bold text-rosa-viejo transition hover:bg-blush">Quitar imagen</button>
                        </form>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</main>
@endsection
