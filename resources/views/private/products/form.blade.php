@extends('layouts.private')

@php
    $isEditing = (bool) $product;
    $initial = [
        'category_id' => old('category_id', $product?->category_id ?? ''),
        'name' => old('name', $product?->name ?? ''),
        'description' => old('description', $product?->description ?? ''),
        'storage_instructions' => old('storage_instructions', $product?->storage_instructions ?? ''),
        'is_featured' => (bool) old('is_featured', $product?->is_featured ?? false),
        'is_custom' => (bool) old('is_custom', $product?->is_custom ?? false),
        'active' => (bool) old('active', $product?->active ?? true),
        'ingredients_text' => old('ingredients_text', $product?->ingredients?->pluck('name')->implode("\n") ?? ''),
        'sizes' => old('sizes', $product?->sizes?->map(fn ($size) => ['id' => $size->id, 'label' => $size->label, 'price' => $size->price])->values()->all() ?: [['id' => '', 'label' => '', 'price' => '']]),
    ];
@endphp

@section('title', $isEditing ? 'Editar producto · Delicias Sn' : 'Nuevo producto · Delicias Sn')

@section('content')
<main class="admin-form min-h-screen bg-crema px-4 py-6 text-ink sm:px-6 lg:px-10" x-data="productEditor(@js($initial))">
    <div class="mx-auto max-w-5xl">
        <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <div class="flex flex-wrap items-center gap-4"><a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#64747A] hover:text-[#263238]">← Dashboard</a><a href="{{ route('private.products.index') }}" class="text-sm font-semibold text-[#64747A] hover:text-[#263238]">Volver a productos</a></div>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-[#263238] sm:text-4xl">{{ $isEditing ? 'Editar producto' : 'Nuevo producto' }}</h1>
                <p class="mt-2 text-sm text-[#263238]/60">Completá cada paso. Los campos obligatorios están indicados dentro del formulario.</p>
            </div>
            <span class="rounded-full bg-[#E9EFF0] px-4 py-2 text-xs font-bold uppercase tracking-[0.12em] text-[#64747A]">Catálogo</span>
        </header>

        <nav class="mt-6 grid gap-2 rounded-2xl border border-[#6B2D3C]/10 bg-white p-2 sm:grid-cols-3" aria-label="Pasos del producto">
            <button type="button" @click="goToStep(1)" :class="currentStep === 1 ? 'bg-[#6B2D3C] text-white shadow-sm' : 'text-[#64747A] hover:bg-[#F5F1EC]'" class="rounded-xl px-4 py-3 text-left transition">
                <span class="block text-xs font-bold uppercase tracking-[0.12em]">Paso 1</span>
                <span class="mt-1 block text-sm font-bold">Producto</span>
            </button>
            <button type="button" @click="goToStep(2)" :disabled="maxStep < 2" :class="currentStep === 2 ? 'bg-[#6B2D3C] text-white shadow-sm' : 'text-[#64747A] hover:bg-[#F5F1EC] disabled:cursor-not-allowed disabled:opacity-45'" class="rounded-xl px-4 py-3 text-left transition">
                <span class="block text-xs font-bold uppercase tracking-[0.12em]">Paso 2</span>
                <span class="mt-1 block text-sm font-bold">Encargo</span>
            </button>
            <button type="button" @click="goToStep(3)" :disabled="maxStep < 3" :class="currentStep === 3 ? 'bg-[#6B2D3C] text-white shadow-sm' : 'text-[#64747A] hover:bg-[#F5F1EC] disabled:cursor-not-allowed disabled:opacity-45'" class="rounded-xl px-4 py-3 text-left transition">
                <span class="block text-xs font-bold uppercase tracking-[0.12em]">Paso 3</span>
                <span class="mt-1 block text-sm font-bold">Publicación</span>
            </button>
        </nav>

        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-[#E3B2A8] bg-[#FFF1EE] px-4 py-3 text-sm text-[#8A3C2E]" role="alert">
                <p class="font-bold">Revisá estos campos:</p>
                <ul class="mt-1 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ $isEditing ? route('private.products.update', $product) : route('private.products.store') }}" method="POST" enctype="multipart/form-data" class="mt-6">
            @csrf
            @if ($isEditing) @method('PUT') @endif

            <div x-show="currentStep === 1" x-cloak x-transition.opacity data-product-step="1">
                <section class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,56,0.06)] sm:p-8">
                    <div class="border-b border-[#263238]/10 pb-5">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#64747A]">Información principal</p>
                        <h2 class="mt-1 text-xl font-bold text-[#263238]">Qué vas a ofrecer</h2>
                        <p class="mt-2 text-sm leading-6 text-[#263238]/60">Estos datos forman la presentación del producto en la página.</p>
                    </div>
                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <label class="field-label">Nombre del producto<input x-model="name" name="name" required class="field-input" placeholder="Ej: Torta de chocolate"></label>
                        <label class="field-label">Categoría<select x-model="category_id" name="category_id" required class="field-input"><option value="">Elegí una categoría</option>@foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></label>
                        <label class="field-label sm:col-span-2">Descripción<textarea x-model="description" name="description" required rows="5" class="field-input resize-y" placeholder="Contá sabores, rellenos y detalles del producto"></textarea></label>
                        <label class="field-label sm:col-span-2">Cómo conservarlo<textarea x-model="storage_instructions" name="storage_instructions" required rows="2" class="field-input resize-y" placeholder="Ej: conservar refrigerado y retirar 20 minutos antes"></textarea></label>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3 border-t border-[#263238]/10 pt-5">
                        <label class="check-label"><input type="hidden" name="active" value="0"><input x-model="active" type="checkbox" name="active" value="1" class="check-input"> Visible en la página</label>
                        <label class="check-label"><input type="hidden" name="is_featured" value="0"><input x-model="is_featured" type="checkbox" name="is_featured" value="1" class="check-input"> Destacado</label><span class="w-full text-xs text-[#64747A]">Podés elegir hasta 4 productos destacados.</span>
                        <label class="check-label"><input type="hidden" name="is_custom" value="0"><input x-model="is_custom" type="checkbox" name="is_custom" value="1" class="check-input"> Producto personalizado</label>
                    </div>
                    <div class="mt-8 flex justify-end border-t border-[#263238]/10 pt-5">
                        <button type="button" @click="nextStep()" class="rounded-xl bg-[#6B2D3C] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#3B2A2A]">Siguiente: encargo →</button>
                    </div>
                </section>
            </div>

            <div x-show="currentStep === 2" x-cloak x-transition.opacity data-product-step="2">
                <div class="space-y-5">
                    <section class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,56,0.06)] sm:p-8">
                        <div class="flex flex-col justify-between gap-3 border-b border-[#263238]/10 pb-5 sm:flex-row sm:items-end">
                            <div><p class="text-xs font-bold uppercase tracking-[0.14em] text-[#64747A]">Variantes por encargo</p><h2 class="mt-1 text-xl font-bold text-[#263238]">Tamaños y precios</h2><p class="mt-2 text-sm leading-6 text-[#263238]/60">Agregá al menos un tamaño. El cliente elegirá una opción al pedir.</p></div>
                            <button type="button" @click="addSize()" class="rounded-xl bg-[#E9EFF0] px-4 py-2.5 text-sm font-bold text-[#263238] hover:bg-[#D8E2E4]">+ Agregar tamaño</button>
                        </div>
                        <div class="mt-5 space-y-3">
                            <template x-for="(size, index) in sizes" :key="size.uid">
                                <div class="grid gap-3 rounded-2xl bg-[#F5F1EC] p-4 sm:grid-cols-[1.4fr_1fr_auto] sm:items-end">
                                    <input type="hidden" :name="`sizes[${index}][id]`" x-model="size.id">
                                    <label class="field-label">Tamaño<input x-model="size.label" :name="`sizes[${index}][label]`" required class="field-input" placeholder="18 cm / 12 porciones"></label>
                                    <label class="field-label">Precio<input x-model="size.price" :name="`sizes[${index}][price]`" required type="number" min="1" class="field-input" placeholder="32000"></label>
                                    <button type="button" @click="removeSize(index)" :disabled="sizes.length === 1" class="rounded-xl px-3 py-3 text-sm font-bold text-[#A85C22] hover:bg-[#FFF1EE] disabled:cursor-not-allowed disabled:opacity-30">Quitar</button>
                                </div>
                            </template>
                        </div>
                    </section>

                </div>
                <div class="mt-5 flex justify-between rounded-2xl border border-[#263238]/10 bg-white p-3 shadow-[0_10px_30px_rgba(38,50,56,0.08)]">
                    <button type="button" @click="previousStep()" class="rounded-xl px-5 py-3 text-sm font-bold text-[#64747A] hover:bg-[#F5F1EC]">← Anterior</button>
                    <button type="button" @click="nextStep()" class="rounded-xl bg-[#6B2D3C] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#3B2A2A]">Siguiente: publicación →</button>
                </div>
            </div>

            <div x-show="currentStep === 3" x-cloak x-transition.opacity data-product-step="3">
                <section class="grid gap-5 lg:grid-cols-2">
                    <div class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,56,0.06)] sm:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#64747A]">Información del producto</p><h2 class="mt-1 text-xl font-bold text-[#263238]">Ingredientes y alérgenos</h2><p class="mt-2 text-sm leading-6 text-[#263238]/60">Ayuda a tus clientes a conocer qué contiene el producto.</p>
                        <label class="field-label mt-5">Ingredientes<textarea x-model="ingredients_text" name="ingredients_text" maxlength="2000" rows="5" class="field-input resize-y" placeholder="Harina 0000\nHuevos\nDulce de leche"></textarea><span class="text-xs font-medium text-[#64747A]">Escribí un ingrediente por línea o separalos con comas.</span></label>
                    </div>
                    <div class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,56,0.06)] sm:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#64747A]">Imagen real</p><h2 class="mt-1 text-xl font-bold text-[#263238]">Foto del producto</h2><p class="mt-2 text-sm leading-6 text-[#263238]/60">Una foto clara ayuda a vender y se mostrará en la página.</p>
                        @if ($product?->image)<img src="{{ product_image_url($product->image) }}" alt="{{ $product->name }}" class="mt-5 aspect-[1.4] w-full rounded-2xl object-cover">@else<div class="mt-5 grid aspect-[1.4] place-items-center rounded-2xl bg-[#E9EFF0] text-center text-sm font-semibold text-[#64747A]">Todavía no hay una imagen cargada</div>@endif
                        <label class="field-label mt-5">Subir o reemplazar<input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full cursor-pointer rounded-xl border border-dashed border-[#263238]/20 bg-[#F5F1EC] p-2.5"></label>
                    </div>
                </section>
                <div class="mt-5 flex flex-col justify-between gap-3 rounded-2xl border border-[#263238]/10 bg-white p-3 shadow-[0_10px_30px_rgba(38,50,56,0.08)] sm:flex-row">
                    <button type="button" @click="previousStep()" class="rounded-xl px-5 py-3 text-sm font-bold text-[#64747A] hover:bg-[#F5F1EC]">← Anterior</button>
                    <div class="flex flex-col gap-3 sm:flex-row"><a href="{{ route('private.products.index') }}" class="rounded-xl px-5 py-3 text-center text-sm font-bold text-[#64747A] hover:bg-[#F5F1EC]">Cancelar</a><button type="submit" class="rounded-xl bg-[#263238] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#455A64]">{{ $isEditing ? 'Guardar cambios' : 'Crear producto' }}</button></div>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection

@push('head')
<style>
    [x-cloak] { display: none !important; }
    .admin-form section { border-color: rgba(107,45,60,.16); border-left: 5px solid #F4D9D5; box-shadow: 0 8px 24px rgba(38,50,56,.045); }
    .admin-form section > div:first-child { border-color: rgba(38,50,56,.12); }
    .field-label { display: grid; gap: .55rem; border: 2px solid rgba(107,45,60,.14); border-radius: 1rem; background: #fffaf6; color: #3B2A2A; font-size: .92rem; font-weight: 750; letter-spacing: .01em; line-height: 1.35; padding: .85rem .9rem; transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease; }
    .field-label:focus-within { border-color: #C98B8B; background: #fff; box-shadow: 0 0 0 3px rgba(201,139,139,.16); }
    .field-label:has([required])::after { content: 'Campo obligatorio'; color: rgba(107,45,60,.6); font-size: .68rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
    .field-label:has([name="name"])::after { content: 'Nombre visible para tus clientes · obligatorio'; }
    .field-label:has([name="category_id"])::after { content: 'Define dónde aparece en el catálogo · obligatorio'; }
    .field-label:has([name="description"])::after { content: 'Contá sabores, rellenos y detalles · obligatorio'; }
    .field-label:has([name="storage_instructions"])::after { content: 'Indicá cómo conservarlo después de retirarlo · obligatorio'; }
    .field-label:has([name$="[label]"])::after { content: 'Ejemplo: 18 cm / 12 porciones · obligatorio'; }
    .field-label:has([name$="[price]"])::after { content: 'Precio final en pesos argentinos · obligatorio'; }
    .field-label:has([name="image"])::after { content: 'JPG, PNG o WebP · máximo 5 MB'; }
    .field-input { display: block; width: 100%; min-height: 3.35rem; margin-top: 0; border: 2px solid rgba(107,45,60,.18); border-radius: .85rem; background: #fffdfa; color: #3B2A2A; padding: .85rem 1rem; font-size: 1rem; font-weight: 550; line-height: 1.45; outline: none; box-shadow: 0 1px 2px rgba(59,42,42,.04); transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease, transform 160ms ease; }
    .field-input:hover { border-color: rgba(107,45,60,.36); background: #fff; }
    .field-input:focus-visible { border-color: #6B2D3C; background: #fff; box-shadow: 0 0 0 4px rgba(201,139,139,.24), 0 2px 5px rgba(59,42,42,.08); transform: translateY(-1px); }
    .field-input::placeholder { color: rgba(59,42,42,.42); }
    textarea.field-input { min-height: 8rem; line-height: 1.55; }
    select.field-input { cursor: pointer; }
    input[type="file"] { border: 1px dashed rgba(107,45,60,.22); border-radius: .75rem; background: rgba(244,217,213,.28); color: rgba(59,42,42,.68); padding: .65rem .75rem; font-size: .85rem; }
    input[type="file"]::file-selector-button { margin-right: .65rem; border: 0; border-radius: .55rem; background: #F4D9D5; color: #6B2D3C; padding: .5rem .75rem; font-size: .78rem; font-weight: 700; cursor: pointer; }
    input[type="file"]:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(201,139,139,.2); }
    .check-label { display: inline-flex; min-height: 2.75rem; align-items: center; gap: .6rem; border: 1px solid rgba(107,45,60,.1); border-radius: .8rem; background: #fffaf6; color: rgba(59,42,42,.82); font-size: .9rem; font-weight: 650; padding: .6rem .75rem; transition: border-color 160ms ease, background 160ms ease; }
    .check-label:hover { border-color: rgba(107,45,60,.28); background: #fff; }
    .check-input { accent-color: #6B2D3C; height: 1rem; width: 1rem; }
</style>
@endpush
