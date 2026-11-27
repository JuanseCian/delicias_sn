<div x-show="selectedProduct" x-cloak x-transition.opacity class="product-modal-backdrop fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-ink/55 p-4 pb-32 sm:p-8 sm:pb-36" @keydown.escape.window="closeProduct()" @click.self="closeProduct()" role="dialog" aria-modal="true" aria-labelledby="product-modal-title">
    <div x-show="selectedProduct" x-transition class="product-modal my-5 grid w-full max-w-4xl overflow-hidden rounded-[2rem] bg-crema shadow-2xl md:grid-cols-2" @click.stop>
        <div class="relative grid min-h-72 place-items-center bg-blush p-8 md:min-h-full">
            <template x-if="selectedProduct?.image"><img :src="selectedProduct?.image" :alt="selectedProduct?.name" class="absolute inset-0 h-full w-full object-cover"></template>
            <template x-if="!selectedProduct?.image"><span class="relative text-center font-script text-6xl text-bordo/65" x-text="selectedProduct?.name"></span></template>
            <button type="button" @click="closeProduct()" class="absolute right-4 top-4 grid h-10 w-10 place-items-center rounded-full bg-crema/90 text-2xl leading-none text-bordo" aria-label="Cerrar detalle">&times;</button>
        </div>
        <div class="max-h-[75vh] overflow-y-auto p-6 sm:p-9">
            <p class="eyebrow" x-text="selectedProduct?.category"></p>
            <h2 id="product-modal-title" class="mt-2 font-display text-3xl text-bordo" x-text="selectedProduct?.name"></h2>
            <p class="mt-4 text-sm leading-7 text-ink/70" x-text="selectedProduct?.description"></p>
            <div class="mt-6">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-bordo">Elegí el tamaño</p>
                <div class="mt-3 grid gap-2">
                    <template x-for="size in (selectedProduct?.sizes || [])" :key="size.id">
                        <button type="button" @click="selectedSize = size.id; detailQuantity = 1" :class="selectedSize === size.id ? 'border-bordo bg-blush' : 'border-bordo/10 bg-white'" class="flex items-center justify-between rounded-2xl border px-4 py-3 text-left transition"><span class="block text-sm" x-text="size.label"></span><strong class="text-sm text-bordo" x-text="money(size.price)"></strong></button>
                    </template>
                </div>
            </div>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <label class="block"><span class="form-label">Cantidad</span><input type="number" min="1" max="50" x-model.number="detailQuantity" class="form-input w-full"></label>
                <label class="block"><span class="form-label">Nota (opcional)</span><input type="text" maxlength="500" x-model="detailNote" class="form-input w-full" placeholder="Ej: mensaje, colores..."></label>
            </div>
            <button type="button" @click="addSelectedToCart()" :disabled="!selectedSize || detailQuantity < 1 || detailQuantity > 50" class="btn-primary mt-6 w-full justify-center disabled:cursor-not-allowed disabled:opacity-40">Agregar al carrito <span aria-hidden="true">+</span></button>
        </div>
    </div>
</div>
