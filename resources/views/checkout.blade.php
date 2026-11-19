@extends('layouts.delicias')

@section('title', 'Reservá tu pedido · Delicias Sn')

@section('content')
<section class="px-4 pb-20 pt-32 sm:px-6 sm:pb-28 sm:pt-40 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="max-w-2xl">
            <p class="section-kicker">Un último paso</p>
            <h1 class="mt-5 font-display text-5xl tracking-[-0.04em] text-bordo sm:text-6xl">Reservá tu <span class="font-script text-6xl font-normal text-rosa-viejo">fecha</span></h1>
            <p class="mt-5 text-base leading-7 text-ink/65">Completá tus datos y nos ponemos a preparar todo.</p>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-start">
            <form method="POST" action="{{ route('orders.store') }}" class="checkout-form rounded-[2rem] bg-white p-5 shadow-card sm:p-8" @submit="if (!validateCheckout()) { $event.preventDefault(); }" novalidate>
                @csrf
                <input type="hidden" name="items" :value="JSON.stringify(cart)">

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-800" role="alert">
                        <p class="font-bold">Revisá los campos marcados:</p>
                    </div>
                @endif

                <div class="flex items-center justify-between border-b border-bordo/10 pb-5">
                    <h2 class="font-display text-2xl text-bordo">Tus datos</h2>
                    <span class="rounded-full bg-blush px-3 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-bordo">Paso 1 de 2</span>
                </div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="block sm:col-span-2">
                        <span class="form-label">Nombre y apellido</span>
                        <input data-checkout-field="customer_name" name="customer_name" x-model="checkout.customer_name" value="{{ old('customer_name') }}" required class="form-input" placeholder="¿Cómo te llamás?">
                        <span x-show="formErrors.customer_name" x-text="formErrors.customer_name" class="field-error" role="alert"></span>
                        @error('customer_name')<span class="field-error" role="alert">{{ $message }}</span>@enderror
                    </label>
                    <label class="block">
                        <span class="form-label">Teléfono / WhatsApp</span>
                        <input data-checkout-field="phone" name="phone" x-model="checkout.phone" value="{{ old('phone') }}" required class="form-input" placeholder="11 1234 5678">
                        <span x-show="formErrors.phone" x-text="formErrors.phone" class="field-error" role="alert"></span>
                        @error('phone')<span class="field-error" role="alert">{{ $message }}</span>@enderror
                    </label>
                    <label class="block">
                        <span class="form-label">¿Cómo lo recibís?</span>
                        <select name="delivery_type" x-model="checkout.delivery_type" class="form-input"><option value="envio">Envío a domicilio</option><option value="retiro">Retiro por Jujuy 422</option></select>
                    </label>
                    <label class="block sm:col-span-2" x-show="checkout.delivery_type === 'envio'">
                        <span class="form-label">Dirección de entrega</span>
                        <input data-checkout-field="address" name="address" x-model="checkout.address" :required="checkout.delivery_type === 'envio'" value="{{ old('address') }}" class="form-input" placeholder="Calle, número y localidad">
                        <span x-show="formErrors.address" x-text="formErrors.address" class="field-error" role="alert"></span>
                        @error('address')<span class="field-error" role="alert">{{ $message }}</span>@enderror
                    </label>
                </div>

                <div class="mt-9 flex items-center justify-between border-b border-bordo/10 pb-5">
                    <div><h2 class="font-display text-2xl text-bordo">Fecha y entrega</h2></div>
                    <span class="rounded-full bg-blush px-3 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-bordo">Paso 2 de 2</span>
                </div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <label class="block">
                        <span class="form-label">Fecha de entrega</span>
                        <input data-checkout-field="delivery_date" type="date" name="delivery_date" x-model="checkout.delivery_date" :min="minimumDateString" @change="validateDate(); normalizeTimeSlot()" required value="{{ old('delivery_date') }}" class="form-input">
                        <span x-show="formErrors.delivery_date" x-text="formErrors.delivery_date" class="field-error" role="alert"></span>
                        @error('delivery_date')<span class="field-error" role="alert">{{ $message }}</span>@enderror
                    </label>
                    <label class="block">
                        <span class="form-label">Franja horaria</span>
                        <select name="time_slot" x-model="checkout.time_slot" class="form-input"><template x-for="slot in availableTimeSlots" :key="slot"><option :value="slot" x-text="slot"></option></template></select>
                    </label>
                    <div class="sm:col-span-2 rounded-2xl bg-blush/60 p-4 text-sm leading-6 text-bordo">
                        <strong>Primera fecha disponible:</strong> <span x-text="minimumDateLabel"></span>
                        <p class="mt-1 text-xs text-ink/60">Calculada según el producto que más anticipación necesita. También podés elegir un domingo.</p>
                        <p x-show="dateError" x-text="dateError" class="mt-2 font-semibold text-red-700"></p>
                    </div>
                    <label class="block sm:col-span-2">
                        <span class="form-label">Notas del pedido <span class="font-normal normal-case tracking-normal text-ink/45">(opcional)</span></span>
                        <textarea name="notes" x-model="checkout.notes" class="form-input min-h-24 resize-y" maxlength="500" placeholder="Mensaje, detalles o preferencias"></textarea>
                        @error('notes')<span class="field-error" role="alert">{{ $message }}</span>@enderror
                    </label>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="btn-primary flex-1">Confirmar reserva <span aria-hidden="true">→</span></button>
                    <button type="button" @click="openWhatsApp()" class="btn-secondary flex-1">Seguir por WhatsApp <span aria-hidden="true">↗</span></button>
                </div>
            </form>

            <aside class="rounded-[2rem] bg-bordo p-5 text-crema shadow-soft sm:p-8 lg:sticky lg:top-28">
                <p class="eyebrow !text-blush">Tu pedido</p>
                <h2 class="mt-2 font-display text-3xl">Todo listo para vos</h2>
                <div class="mt-7 space-y-4">
                    <template x-if="cart.length === 0"><p class="rounded-2xl bg-white/10 p-4 text-sm leading-6 text-crema/70">Tu carrito está vacío. Volvé al catálogo para elegir tus favoritos.</p></template>
                    <template x-for="item in cart" :key="item.key"><div class="flex gap-3 border-b border-white/10 pb-4"><div class="product-thumb product-thumb-dark"><template x-if="productFor(item.product_id)?.image"><img :src="productFor(item.product_id)?.image" :alt="productFor(item.product_id)?.name" class="h-full w-full object-cover"></template><template x-if="!productFor(item.product_id)?.image"><span class="font-script text-xl text-blush/70">♡</span></template></div><div class="min-w-0 flex-1"><p class="font-display text-lg" x-text="productFor(item.product_id)?.name"></p><p class="text-xs text-crema/55" x-text="`${sizeFor(item)?.label} · x${item.quantity}`"></p></div><strong class="text-sm" x-text="money(itemTotal(item))"></strong></div></template>
                </div>
                <div class="mt-7 flex justify-between border-t border-white/15 pt-5 text-base font-bold"><span>Total</span><span x-text="money(subtotal)"></span></div>
                <a href="{{ route('home') }}#catalogo" class="mt-5 block text-center text-xs font-bold uppercase tracking-[0.12em] text-blush hover:text-white">← Seguir mirando productos</a>
            </aside>
        </div>
    </div>
</section>
@endsection

@push('head')
<style>
    .form-label { display: block; color: #6b2d3c; font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
    .form-input { display: block; width: 100%; margin-top: .5rem; border: 1px solid rgba(107,45,60,.14); border-radius: 1rem; background: #fbf5ee; color: #3b2a2a; padding: .8rem 1rem; font-size: .9rem; outline: none; }
    .form-input:focus { border-color: #c98b8b; box-shadow: 0 0 0 3px rgba(201,139,139,.2); }
    .field-error { display: block; margin-top: .45rem; color: #a33f42; font-size: .76rem; font-weight: 600; line-height: 1.35; }
    .form-input:invalid:not(:placeholder-shown) { border-color: #d78383; }
</style>
@endpush
