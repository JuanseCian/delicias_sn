@php
    $featuredCount = min($featured->count(), 4);
@endphp

<section id="destacados" class="overflow-hidden {{ config('delicias.christmas_theme_enabled') ? 'bg-pino' : 'bg-bordo' }} px-4 py-12 text-crema sm:px-6 sm:py-16 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div>
            <p class="section-kicker !text-blush before:!bg-blush">Selección de la casa</p>
            <h2 class="mt-3 font-display text-3xl leading-tight sm:text-4xl">Los más <span class="font-script text-5xl font-normal text-blush">elegidos</span></h2>
        </div>

        <div class="mt-6 grid gap-3 lg:gap-4 {{ $featuredCount === 1 ? 'lg:grid-cols-1' : 'lg:grid-cols-2' }}">
            @foreach ($featured->take(4) as $product)
                @php
                    $isSingle = $featuredCount === 1;
                    $isLead = $featuredCount === 3 && $loop->first;
                    $cardClass = match (true) {
                        $isSingle => 'lg:grid-cols-[1.1fr_0.9fr]',
                        $isLead => 'lg:row-span-2 lg:grid-rows-[1fr_auto]',
                        default => '',
                    };
                    $imageClass = $isSingle ? 'aspect-[1.35] lg:aspect-auto lg:min-h-[16rem]' : ($isLead ? 'aspect-[1.2] lg:aspect-auto lg:min-h-full' : 'aspect-[1.5]');
                @endphp

                <article class="group grid overflow-hidden rounded-[2rem] border border-blush/15 bg-white/10 shadow-[0_18px_55px_rgba(38,12,20,0.18)] backdrop-blur-sm {{ $cardClass }}">
                    <div class="featured-product-frame relative {{ $imageClass }}">
                        @if ($product->image)
                            <img src="{{ product_image_url($product->image) }}" alt="{{ $product->name }} destacado" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                        @else
                            <span class="px-6 text-center font-script text-6xl text-bordo">{{ $product->name }}</span>
                        @endif
                        <span class="absolute left-4 top-4 rounded-full bg-crema/90 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.1em] text-bordo">Destacado</span>
                    </div>
                    <div class="flex flex-col justify-between p-5 sm:p-6">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-blush/70">{{ $product->category->name }}</p>
                            <h3 class="mt-2 font-display text-2xl leading-tight text-white sm:text-3xl">{{ $product->name }}</h3>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-blush/15 pt-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-crema/45">Desde</p>
                                <p class="mt-1 font-display text-2xl text-white">{{ format_price($product->starting_price) }}</p>
                            </div>
                            <button type="button" @click="openProduct({{ $product->id }})" aria-label="Ver opciones de {{ $product->name }}" class="rounded-full bg-blush px-5 py-3 text-xs font-bold uppercase tracking-[0.1em] text-bordo transition hover:bg-white">Ver opciones <span aria-hidden="true">↗</span></button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
