@extends('layouts.private')

@section('title', 'Categorías · Delicias Sn')

@section('content')
<main class="min-h-screen bg-crema px-4 py-6 text-ink sm:px-6 lg:px-10">
    <div class="mx-auto max-w-4xl">
        <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <div class="flex flex-wrap items-center gap-4"><a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#64747A] hover:text-[#263238]">← Dashboard</a><a href="{{ route('private.products.index') }}" class="text-sm font-semibold text-[#64747A] hover:text-[#263238]">Volver a productos</a></div>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-[#263238] sm:text-4xl">Categorías</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#263238]/60">Las categorías definen el orden del catálogo y cuánta anticipación necesitan sus productos.</p>
            </div>
        </header>

        <section class="mt-6 rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,56,0.06)] sm:p-8">
            <h2 class="text-xl font-bold text-[#263238]">Nueva categoría</h2>
            <form action="{{ route('private.categories.store') }}" method="POST" class="mt-4 grid gap-4 sm:grid-cols-3">
                @csrf
                <label class="block sm:col-span-1">
                    <span class="text-xs font-bold uppercase tracking-[0.1em] text-[#64747A]">Nombre</span>
                    <input name="name" value="{{ old('name') }}" required maxlength="80" class="mt-1 block w-full rounded-xl border border-[#263238]/15 bg-[#FBF5EE] px-3 py-2.5 text-sm" placeholder="Ej: Tortas">
                    @error('name')<span class="mt-1 block text-xs font-semibold text-[#A85C22]">{{ $message }}</span>@enderror
                </label>
                <label class="block sm:col-span-1">
                    <span class="text-xs font-bold uppercase tracking-[0.1em] text-[#64747A]">Anticipación (horas)</span>
                    <input name="lead_time_hours" value="{{ old('lead_time_hours') }}" required type="number" min="1" class="mt-1 block w-full rounded-xl border border-[#263238]/15 bg-[#FBF5EE] px-3 py-2.5 text-sm" placeholder="48">
                    @error('lead_time_hours')<span class="mt-1 block text-xs font-semibold text-[#A85C22]">{{ $message }}</span>@enderror
                </label>
                <label class="block sm:col-span-1">
                    <span class="text-xs font-bold uppercase tracking-[0.1em] text-[#64747A]">Orden <span class="font-normal normal-case text-[#64747A]/70">(opcional)</span></span>
                    <input name="order" value="{{ old('order') }}" type="number" min="0" class="mt-1 block w-full rounded-xl border border-[#263238]/15 bg-[#FBF5EE] px-3 py-2.5 text-sm" placeholder="Al final">
                    @error('order')<span class="mt-1 block text-xs font-semibold text-[#A85C22]">{{ $message }}</span>@enderror
                </label>
                <div class="sm:col-span-3">
                    <button type="submit" class="rounded-xl bg-[#6B2D3C] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#3B2A2A]">+ Agregar categoría</button>
                </div>
            </form>
        </section>

        <section class="mt-6 space-y-3">
            @forelse ($categories as $category)
                <article class="flex flex-col justify-between gap-3 rounded-[1.5rem] border border-[#263238]/10 bg-white p-5 shadow-[0_8px_24px_rgba(38,50,56,0.06)] sm:flex-row sm:items-center">
                    <div>
                        <h3 class="text-lg font-bold text-[#263238]">{{ $category->name }}</h3>
                        <p class="mt-1 text-xs text-[#263238]/55">Orden {{ $category->order }} · Anticipación mínima {{ $category->lead_time_hours }} h · {{ $category->products_count }} {{ $category->products_count === 1 ? 'producto' : 'productos' }}</p>
                    </div>
                    <form action="{{ route('private.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            @if ($category->products_count > 0) disabled title="Movés o eliminás sus productos antes de poder quitarla" @endif
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#A85C22] transition hover:bg-[#FFF1EE] disabled:cursor-not-allowed disabled:opacity-35"
                        >Quitar</button>
                    </form>
                </article>
            @empty
                <div class="rounded-[1.5rem] border border-dashed border-[#263238]/15 bg-white p-8 text-center text-sm text-[#263238]/55">Todavía no hay categorías.</div>
            @endforelse
        </section>
    </div>
</main>
@endsection
