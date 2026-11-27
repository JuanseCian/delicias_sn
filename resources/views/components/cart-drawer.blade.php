<div x-show="cartOpen" x-cloak @keydown.escape.window="closeCart()" class="fixed inset-0 z-50">
    <div x-show="cartOpen" x-transition.opacity class="absolute inset-0 bg-ink/50 backdrop-blur-[2px]" @click="closeCart()"></div>

    <aside
        id="cart-dialog"
        x-show="cartOpen"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-[250ms]"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute right-0 top-0 flex h-[100dvh] w-full flex-col overflow-hidden bg-crema shadow-2xl sm:max-w-md"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cart-title"
        aria-describedby="cart-description"
        tabindex="-1"
        @keydown="trapCartFocus($event)"
        @click.stop
    >
        <header class="flex shrink-0 items-center justify-between border-b border-bordo/10 bg-crema/95 px-5 py-4 backdrop-blur sm:px-6">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-blush text-bordo" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h2l1.6 9.1a2 2 0 0 0 2 1.7h7.1a2 2 0 0 0 1.9-1.5L20 8H7"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                </span>
                <div>
                    <p class="eyebrow">Tu selección</p>
                    <h2 id="cart-title" class="mt-1 font-display text-2xl leading-none text-bordo">Carrito <span class="text-lg font-normal text-ink/45" x-text="`(${cartCount})`"></span></h2>
                    <p id="cart-description" class="sr-only">Revisá tus productos antes de continuar el pedido.</p>
                </div>
            </div>
            <button id="cart-close" type="button" @click="closeCart()" class="grid h-10 w-10 place-items-center rounded-full border border-bordo/10 bg-white text-bordo transition hover:bg-blush focus-visible:ring-2 focus-visible:ring-bordo" aria-label="Cerrar carrito">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </header>

        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">
            <template x-if="cart.length === 0">
                <div class="flex min-h-full flex-col items-center justify-center py-8 text-center">
                    <div class="cart-empty-art cart-float relative grid h-32 w-32 place-items-center rounded-[2.5rem] bg-blush/60" aria-hidden="true">
                        <svg class="h-24 w-24" viewBox="0 0 128 128" fill="none">
                            <path d="M33 66h62l-7 43H40l-7-43Z" fill="#6B2D3C" opacity=".92"/>
                            <path d="M39 72h50l-5 31H44l-5-31Z" fill="#F4D9D5"/>
                            <path d="M27 63c0-8 7-14 15-14 2-11 12-19 24-19 10 0 19 6 23 15 2-1 4-1 6-1 8 0 14 6 14 14 0 8-6 14-14 14H41c-8 0-14-3-14-9Z" fill="#FFFDFB" stroke="#C98B8B" stroke-width="3"/>
                            <path d="M59 31c-2-7 1-13 8-17" stroke="#6B2D3C" stroke-width="3" stroke-linecap="round"/>
                            <circle cx="69" cy="14" r="4" fill="#C68B59"/>
                            <path d="M48 82h32M51 94h26" stroke="#C98B8B" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <p class="eyebrow mt-7">Un antojo pendiente</p>
                    <h3 class="mt-2 font-display text-3xl leading-tight text-bordo">Tu carrito está vacío</h3>
                    <a href="{{ route('catalog.index') }}" class="btn-primary mt-7 px-6">Explorar productos <span aria-hidden="true">↗</span></a>
                </div>
            </template>

            <div x-show="cart.length > 0" class="space-y-3">
                <template x-for="item in cart" :key="item.key">
                    <article x-show="true" x-transition:enter="transition ease-out duration-[250ms]" x-transition:enter-start="translate-x-3 opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="-translate-x-3 opacity-0" :class="removingItemKey === item.key ? 'pointer-events-none -translate-x-3 opacity-0' : ''" class="cart-item rounded-[1.5rem] border border-bordo/10 bg-white p-3 shadow-card sm:p-4">
                        <div class="flex gap-3">
                            <div class="product-thumb h-[4.75rem] w-[4.75rem] rounded-2xl sm:h-20 sm:w-20">
                                <template x-if="productFor(item.product_id)?.image">
                                    <img :src="productFor(item.product_id)?.image" :alt="productFor(item.product_id)?.name" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!productFor(item.product_id)?.image">
                                    <svg class="h-10 w-10 text-rosa-viejo/70" viewBox="0 0 48 48" fill="none" aria-hidden="true"><path d="M10 25h28l-3 15H13l-3-15Z" fill="currentColor" opacity=".35"/><path d="M7 24c0-4 3-7 7-7 1-5 5-8 10-8s9 3 10 8c4 0 7 3 7 7 0 4-3 7-7 7H14c-4 0-7-3-7-7Z" fill="#FFF" stroke="currentColor" stroke-width="2"/></svg>
                                </template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="truncate font-display text-lg leading-tight text-bordo" x-text="productFor(item.product_id)?.name"></h3>
                                        <div class="mt-1 flex flex-wrap gap-1.5 text-xs text-ink/55">
                                            <span class="rounded-full bg-crema px-2 py-1" x-text="sizeFor(item)?.label"></span>
                                            <span x-show="colorFor(item)" class="rounded-full bg-crema px-2 py-1" x-text="colorFor(item)?.name"></span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeItem(item.key)" class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-ink/40 transition hover:bg-blush hover:text-bordo focus-visible:ring-2 focus-visible:ring-bordo" :aria-label="`Quitar ${productFor(item.product_id)?.name}`">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                                    </button>
                                </div>
                                <p x-show="item.note" x-text="item.note" class="mt-2 line-clamp-2 border-l-2 border-rosa-viejo/50 pl-2 text-xs italic leading-5 text-ink/55"></p>
                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <div class="flex items-center rounded-full border border-bordo/10 bg-crema p-1" role="group" :aria-label="`Cantidad de ${productFor(item.product_id)?.name}`">
                                        <button type="button" @click="changeQuantity(item.key, -1)" :disabled="item.quantity <= 1" class="grid h-7 w-7 place-items-center rounded-full text-lg leading-none text-bordo transition hover:bg-blush disabled:cursor-not-allowed disabled:opacity-30" aria-label="Restar cantidad">−</button>
                                        <span class="min-w-8 text-center text-sm font-bold text-ink" aria-live="polite" x-text="item.quantity"></span>
                                        <button type="button" @click="changeQuantity(item.key, 1)" :disabled="item.quantity >= 50" class="grid h-7 w-7 place-items-center rounded-full text-lg leading-none text-bordo transition hover:bg-blush disabled:cursor-not-allowed disabled:opacity-30" aria-label="Sumar cantidad">+</button>
                                    </div>
                                    <strong class="text-sm font-bold text-bordo" x-text="money(itemTotal(item))"></strong>
                                </div>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
        </div>

        <footer x-show="cart.length > 0" x-transition.opacity class="shrink-0 border-t border-bordo/10 bg-white/90 px-5 pb-[calc(1rem+env(safe-area-inset-bottom))] pt-4 backdrop-blur sm:px-6 sm:pb-5">
            <div class="mb-4 grid gap-2 rounded-2xl bg-crema px-3 py-2.5 text-xs leading-5 text-ink/60 sm:grid-cols-2 sm:gap-3">
                <p><span class="font-bold text-bordo">Retiro:</span> Jujuy 422</p>
                <p><span class="font-bold text-bordo">Envíos:</span> coordinados por WhatsApp</p>
            </div>
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-ink/45">Subtotal</p>
                    <p class="mt-1 font-display text-2xl text-bordo" x-text="money(subtotal)"></p>
                </div>
                <p class="text-right text-xs leading-5 text-ink/50">La fecha se confirma<br>según elaboración.</p>
            </div>
            <a href="{{ route('checkout') }}" class="btn-primary mt-4 w-full py-3.5">Continuar pedido <span aria-hidden="true">→</span></a>
            <a href="https://wa.me/{{ config('delicias.whatsapp') }}" :href="whatsappUrl()" target="_blank" rel="noopener" class="mt-2.5 flex w-full items-center justify-center gap-2 rounded-full border border-bordo/20 px-5 py-3 text-sm font-bold text-bordo transition hover:bg-blush focus-visible:ring-2 focus-visible:ring-bordo"><span>Encargar por WhatsApp</span><span aria-hidden="true">↗</span></a>
        </footer>
    </aside>
</div>
