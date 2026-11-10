@extends('layouts.private')

@section('title', 'Diseñador · Delicias Sn')

@section('content')
@php
    $kindLabels = ['size' => 'Tamaños', 'coverage' => 'Coberturas', 'color' => 'Colores', 'topping' => 'Decoraciones', 'template' => 'Plantillas'];
@endphp
<main class="admin-products min-h-screen bg-crema px-4 py-6 text-ink sm:px-6 lg:px-10">
    <div class="mx-auto max-w-7xl">
        <header class="admin-surface flex flex-col justify-between gap-5 rounded-[1.75rem] border p-6 sm:flex-row sm:items-end sm:p-8">
            <div><p class="eyebrow">Configuración del módulo</p><h1 class="admin-heading mt-2 text-3xl tracking-tight sm:text-4xl">Diseñador de tortas</h1><p class="admin-muted mt-2 max-w-2xl text-sm leading-6">Administrá opciones, precios y disponibilidad. Las personalizaciones desactivadas no aparecen en el configurador público.</p></div>
            <div class="flex gap-2"><a href="{{ route('dashboard') }}" class="admin-button-soft rounded-xl px-4 py-3 text-sm font-semibold">Panel</a><a href="{{ route('private.products.index') }}" class="admin-button rounded-xl px-5 py-3 text-sm font-bold">Productos</a></div>
        </header>

        <div class="mt-6 grid gap-5 lg:grid-cols-[1.15fr_0.85fr]">
            <section class="space-y-5">
                @foreach ($kindLabels as $kind => $label)
                    <div class="admin-surface rounded-[1.5rem] border p-5 sm:p-6">
                        <div class="flex items-center justify-between gap-3"><div><p class="eyebrow">Catálogo del diseñador</p><h2 class="admin-heading mt-1 text-2xl">{{ $label }}</h2></div><span class="rounded-full bg-blush px-3 py-2 text-xs font-bold text-bordo">{{ $options->where('kind', $kind)->count() }}</span></div>
                        <div class="mt-5 space-y-3">
                            @foreach ($options->where('kind', $kind) as $option)
                                <form action="{{ route('private.customizer.options.update', $option) }}" method="POST" class="grid gap-3 rounded-2xl bg-crema/75 p-3 sm:grid-cols-[1.2fr_1.5fr_8rem_auto] sm:items-end">
                                    @csrf @method('PATCH')
                                    <label class="field-label">Nombre<input name="name" value="{{ $option->name }}" required class="field-input"></label>
                                    <label class="field-label">Descripción<input name="description" value="{{ $option->description }}" class="field-input"></label>
                                    <label class="field-label">Precio<input name="price" value="{{ $option->price }}" required type="number" min="0" class="field-input"></label>
                                    <div class="flex items-center gap-3 sm:flex-col sm:items-stretch"><label class="check-label"><input type="hidden" name="active" value="0"><input type="checkbox" name="active" value="1" class="check-input" @checked($option->active)> Activa</label><button type="submit" class="admin-button rounded-xl px-3 py-2.5 text-xs font-bold">Guardar</button></div>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </section>

            <aside class="space-y-5">
                <section class="admin-surface rounded-[1.5rem] border p-5 sm:p-6">
                    <p class="eyebrow">Nueva opción</p><h2 class="admin-heading mt-1 text-2xl">Ampliar el catálogo</h2><p class="admin-muted mt-2 text-sm leading-6">Usá metadata JSON para propiedades visuales, por ejemplo <code>{"hex":"#F4D9D5"}</code> o <code>{"type":"flower"}</code>.</p>
                    <form action="{{ route('private.customizer.options.store') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <label class="field-label">Tipo<select name="kind" required class="field-input"><option value="size">Tamaño</option><option value="coverage">Cobertura</option><option value="color">Color</option><option value="topping">Decoración</option><option value="template">Plantilla</option></select></label>
                        <label class="field-label">Identificador<input name="slug" required pattern="[a-z0-9-]+" class="field-input" placeholder="flores-blancas"></label>
                        <label class="field-label">Nombre<input name="name" required class="field-input" placeholder="Flores blancas"></label>
                        <label class="field-label">Descripción<input name="description" class="field-input" placeholder="Detalle delicado"></label>
                        <label class="field-label">Precio<input name="price" required type="number" min="0" value="0" class="field-input"></label>
                        <label class="field-label">Metadata JSON<textarea name="metadata" rows="3" class="field-input font-mono text-xs" placeholder='{"type":"flower"}'></textarea></label>
                        <button type="submit" class="admin-button w-full rounded-xl px-4 py-3 text-sm font-bold">Agregar opción</button>
                    </form>
                </section>

                <section class="admin-surface rounded-[1.5rem] border p-5 sm:p-6">
                    <p class="eyebrow">Solicitudes recibidas</p><h2 class="admin-heading mt-1 text-2xl">Ideas de clientes</h2>
                    <div class="mt-5 space-y-3">
                        @forelse ($requests as $request)
                            <article class="rounded-2xl bg-crema/75 p-4"><div class="flex justify-between gap-3"><div><p class="font-bold text-bordo">{{ $request->customer_name ?: 'Sin nombre' }}</p><p class="mt-1 text-xs text-ink/55">{{ $request->phone ?: 'Sin WhatsApp' }} · {{ $request->created_at->format('d/m/Y H:i') }}</p></div><span class="rounded-full bg-blush px-2 py-1 text-[10px] font-bold uppercase text-bordo">{{ $request->status }}</span></div>@if($request->prompt)<p class="mt-3 text-sm leading-5 text-ink/65">{{ $request->prompt }}</p>@endif<p class="mt-3 text-sm font-bold text-bordo">{{ $request->estimated_price ? format_price($request->estimated_price) : 'A cotizar' }}</p></article>
                        @empty
                            <p class="rounded-2xl border border-dashed border-bordo/15 p-4 text-sm text-ink/55">Todavía no hay solicitudes.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</main>
@endsection
