<div x-show="cartOpen" x-cloak class="fixed inset-0 z-50" role="dialog" aria-modal="true" aria-labelledby="cart-title">
    <div x-show="cartOpen" x-transition.opacity class="absolute inset-0 bg-ink/45" @click="cartOpen = false"></div>
    <aside x-show="cartOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="absolute right-0 top-0 flex h-full w-full max-w-md flex-col bg-crema shadow-2xl">
        <div class="flex items-center justify-between border-b border-bordo/10 px-6 py-5">
            <div><p class="eyebrow">Tu selección</p><h2 id="cart-title" class="mt-1 font-display text-2xl text-bordo">Carrito</h2></div>
            <button type="button" @click="cartOpen = false" class="grid h-10 w-10 place-items-center rounded-full bg-blush text-bordo" aria-label="Cerrar carrito"><span class="text-2xl leading-none">&times;</span></button>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <template x-if="cart.length === 0">
                <div class="grid h-full place-items-center py-16 text-center"><div><span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-blush text-3xl">♡</span><h3 class="mt-5 font-display text-2xl text-bordo">Tu carrito está vacío</h3><p class="mt-2 max-w-xs text-sm leading-6 text-ink/60">Elegí algo rico del catálogo y empezamos a preparar tu reserva.</p><button type="button" @click="cartOpen = false; document.querySelector('#catalogo').scrollIntoView({ behavior: 'smooth' })" class="mt-6 rounded-full bg-bordo px-5 py-3 text-sm font-bold text-white">Ver productos</button></div></div>
            </template>
            <div class="space-y-4">
                <template x-for="item in cart" :key="item.key">
                    <article class="rounded-3xl bg-white p-3 shadow-card"><div class="flex gap-3"><div class="product-thumb"> <template x-if="productFor(item.product_id)?.image"><img :src="productFor(item.product_id)?.image" :alt="productFor(item.product_id)?.name" class="h-full w-full object-cover"></template><template x-if="!productFor(item.product_id)?.image"><span class="font-script text-xl text-bordo/55">♡</span></template></div><div class="min-w-0 flex-1"><div class="flex justify-between gap-2"><div><h3 class="font-display text-lg leading-tight text-bordo" x-text="productFor(item.product_id)?.name"></h3><p class="mt-1 text-xs text-ink/55" x-text="sizeFor(item)?.label"></p></div><button type="button" @click="removeItem(item.key)" class="text-xs text-rosa-viejo underline underline-offset-2">Quitar</button></div><div class="mt-3 flex items-center justify-between"><div class="flex items-center gap-2 rounded-full bg-crema px-2 py-1"><button type="button" @click="changeQuantity(item.key, -1)" class="grid h-6 w-6 place-items-center rounded-full text-bordo" aria-label="Restar cantidad">−</button><span class="min-w-4 text-center text-sm font-bold" x-text="item.quantity"></span><button type="button" @click="changeQuantity(item.key, 1)" class="grid h-6 w-6 place-items-center rounded-full text-bordo" aria-label="Sumar cantidad">+</button></div><strong class="text-sm text-bordo" x-text="money(itemTotal(item))"></strong></div></div></div><p x-show="item.note" x-text="item.note" class="mt-3 border-t border-bordo/10 pt-2 text-xs italic text-ink/55"></p></article>
                </template>
            </div>
        </div>
        <div x-show="cart.length > 0" class="border-t border-bordo/10 bg-white px-6 py-5">
            <div class="space-y-2 text-sm"><div class="flex justify-between text-ink/65"><span>Subtotal</span><strong x-text="money(subtotal)"></strong></div><div class="flex justify-between text-bordo"><span>Seña para reservar (50%)</span><strong x-text="money(deposit)"></strong></div><div class="flex justify-between border-t border-bordo/10 pt-3 text-base font-bold text-bordo"><span>Saldo al recibir</span><span x-text="money(balance)"></span></div></div>
            <a href="{{ route('checkout') }}" class="mt-5 block rounded-full bg-bordo px-5 py-4 text-center text-sm font-bold text-white transition hover:bg-ink">Continuar reserva</a>
            <button type="button" @click="openWhatsApp()" class="mt-3 flex w-full items-center justify-center gap-2 rounded-full border border-bordo/20 px-5 py-3 text-sm font-bold text-bordo transition hover:bg-blush"><span>Encargar por WhatsApp</span><span aria-hidden="true">↗</span></button>
        </div>
    </aside>
</div>
