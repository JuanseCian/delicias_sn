@php
    $optionGroups = $customizerOptions->groupBy('kind');
@endphp

<section id="personalizadas" class="scroll-mt-28 px-4 py-20 sm:px-6 sm:py-28 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="max-w-2xl reveal">
            <p class="section-kicker">Diseñá algo único</p>
            <h2 class="mt-4 font-display text-4xl leading-tight tracking-[-0.03em] text-bordo sm:text-5xl">Tu torta,<br><span class="font-script text-6xl font-normal text-rosa-viejo">a tu manera</span></h2>
            <p class="mt-5 max-w-xl text-base leading-7 text-ink/65">Probá tamaños, coberturas, colores y detalles sobre una maqueta 3D. Es una guía visual para explorar tu idea y compartirla con Delicias.</p>
        </div>

        <div data-cake-designer data-whatsapp="{{ config('delicias.whatsapp') }}" class="cake-designer mt-10 overflow-hidden rounded-[2.5rem] border border-bordo/10 bg-white/70 p-3 shadow-soft sm:p-5">
            <div class="cake-mobile-tabs mb-4 grid grid-cols-3 gap-1 rounded-2xl bg-crema p-1 lg:hidden">
                <button type="button" data-cake-mobile-tab="stage" class="cake-mobile-tab">Vista</button>
                <button type="button" data-cake-mobile-tab="controls" class="cake-mobile-tab">Personalizar</button>
                <button type="button" data-cake-mobile-tab="summary" class="cake-mobile-tab">Resumen</button>
            </div>
            <div class="grid gap-4 lg:grid-cols-[minmax(15rem,0.8fr)_minmax(24rem,1.35fr)_minmax(15rem,0.8fr)]">
                <aside data-cake-mobile-section="controls" class="cake-mobile-section cake-designer-panel order-2 rounded-[2rem] bg-crema p-4 sm:p-5 lg:order-1">
                    <div class="flex items-center justify-between gap-3">
                        <div><p class="eyebrow">Configuración</p><h3 class="mt-2 font-display text-2xl text-bordo">Elegí tu estilo</h3></div>
                        <span class="rounded-full bg-blush px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.1em] text-bordo">V1</span>
                    </div>

                    @if (($optionGroups['template'] ?? collect())->isNotEmpty())
                        <div class="mt-6"><p class="cake-designer-label">Diseños rápidos</p><div class="mt-2 grid grid-cols-2 gap-2">@foreach ($optionGroups['template'] as $option)<button type="button" data-cake-option data-option-kind="template" data-option-id="{{ $option['id'] }}" class="cake-option"><span class="block font-semibold">{{ $option['name'] }}</span><span class="mt-0.5 block text-[10px] text-ink/50">{{ $option['description'] }}</span></button>@endforeach</div></div>
                    @endif

                    <div class="mt-6"><p class="cake-designer-label">Tamaño</p><div class="mt-2 grid grid-cols-3 gap-2">@foreach ($optionGroups['size'] ?? [] as $option)<button type="button" data-cake-option data-option-kind="size" data-option-id="{{ $option['id'] }}" class="cake-option"><span class="block font-display text-lg">{{ $option['name'] }}</span><span class="mt-0.5 block text-[10px] text-ink/50">{{ $option['description'] }}</span></button>@endforeach</div></div>

                    <div class="mt-6"><p class="cake-designer-label">Cobertura</p><div class="mt-2 grid gap-2">@foreach ($optionGroups['coverage'] ?? [] as $option)<button type="button" data-cake-option data-option-kind="coverage" data-option-id="{{ $option['id'] }}" class="cake-option text-left"><span class="block font-semibold">{{ $option['name'] }}</span><span class="mt-0.5 block text-[10px] text-ink/50">{{ $option['description'] }}</span></button>@endforeach</div></div>

                    <div class="mt-6"><p class="cake-designer-label">Colores <span class="font-normal normal-case tracking-normal text-ink/45">hasta 2</span></p><div class="mt-2 flex flex-wrap gap-2">@foreach ($optionGroups['color'] ?? [] as $option)<button type="button" data-cake-option data-option-kind="color" data-option-id="{{ $option['id'] }}" class="cake-color-option" title="{{ $option['name'] }}"><span class="h-7 w-7 rounded-full border border-ink/10" style="background-color: {{ $option['metadata']['hex'] ?? '#FFFFFF' }}"></span><span class="sr-only">{{ $option['name'] }}</span></button>@endforeach</div></div>

                    <div class="mt-6"><p class="cake-designer-label">Decoraciones</p><div class="mt-2 grid grid-cols-2 gap-2">@foreach ($optionGroups['topping'] ?? [] as $option)<button type="button" data-cake-option data-option-kind="topping" data-option-id="{{ $option['id'] }}" class="cake-option text-left"><span class="block text-xs font-semibold">{{ $option['name'] }}</span><span class="mt-0.5 block text-[10px] text-ink/50">Disponible para personalizar</span></button>@endforeach</div></div>

                    <button type="button" data-cake-reset class="mt-6 w-full rounded-xl border border-bordo/15 px-4 py-3 text-xs font-bold uppercase tracking-[0.1em] text-bordo transition hover:bg-blush">Restablecer diseño</button>
                </aside>

                <div data-cake-mobile-section="stage" class="cake-mobile-section order-1 flex min-h-[30rem] flex-col rounded-[2rem] bg-[#F4D9D5]/55 p-3 sm:min-h-[38rem] sm:p-5 lg:order-2">
                    <div class="flex items-center justify-between gap-3 px-2 py-1"><div><p class="eyebrow">Vista previa</p><p class="mt-1 text-xs text-ink/50">Arrastrá para rotar · rueda para acercar</p></div><span data-cake-status class="rounded-full bg-white/75 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.08em] text-bordo">Listo para crear</span></div>
                    <div class="cake-stage relative mt-3 min-h-0 flex-1 overflow-hidden rounded-[1.75rem] bg-gradient-to-b from-[#fffaf6] to-[#f4d9d5]" data-cake-stage>
                        <canvas data-cake-canvas class="absolute inset-0 h-full w-full"></canvas>
                        <div data-cake-fallback hidden class="absolute inset-0 grid place-items-center p-8 text-center"><div><span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blush text-2xl text-bordo">♡</span><p class="mt-4 font-display text-2xl text-bordo">Tu diseño está listo</p><p class="mt-2 max-w-xs text-sm leading-6 text-ink/60">Tu navegador no habilita la vista 3D, pero podés completar la configuración y enviarnos tu idea.</p></div></div>
                        <div class="pointer-events-none absolute inset-x-0 bottom-4 text-center text-[10px] font-bold uppercase tracking-[0.16em] text-bordo/35">Delicias Sn · Diseño orientativo</div>
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-2 px-1"><p class="text-xs text-ink/55">La maqueta cambia en tiempo real.</p><div class="flex gap-2"><button type="button" data-cake-camera class="rounded-full bg-white/80 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.08em] text-bordo transition hover:bg-white">Vista inicial</button><button type="button" data-cake-download class="rounded-full bg-bordo px-3 py-2 text-[10px] font-bold uppercase tracking-[0.08em] text-white transition hover:bg-ink">Descargar</button></div></div>
                </div>

                <aside data-cake-mobile-section="summary" class="cake-mobile-section order-3 rounded-[2rem] bg-bordo p-5 text-crema sm:p-6 lg:order-3">
                    <p class="eyebrow !text-blush">Resumen</p><h3 class="mt-2 font-display text-3xl">Tu propuesta</h3>
                    <dl class="mt-6 space-y-3 border-b border-white/15 pb-5 text-sm"><div class="flex justify-between gap-3"><dt class="text-crema/60">Tamaño</dt><dd data-cake-summary="size" class="text-right font-semibold">-</dd></div><div class="flex justify-between gap-3"><dt class="text-crema/60">Cobertura</dt><dd data-cake-summary="coverage" class="text-right font-semibold">-</dd></div><div class="flex justify-between gap-3"><dt class="text-crema/60">Colores</dt><dd data-cake-summary="colors" class="text-right font-semibold">-</dd></div><div class="flex justify-between gap-3"><dt class="text-crema/60">Porciones</dt><dd data-cake-summary="servings" class="text-right font-semibold">-</dd></div><div class="flex justify-between gap-3"><dt class="text-crema/60">Decoraciones</dt><dd data-cake-summary="toppings" class="max-w-[9rem] text-right font-semibold">Ninguna</dd></div></dl>
                    <div class="mt-5 rounded-2xl bg-white/10 p-4"><p class="text-xs font-bold uppercase tracking-[0.14em] text-blush">Diseño visual</p><p class="mt-2 text-sm leading-6 text-crema/70">Usá esta maqueta para definir la idea. Después podés compartirla con Delicias para conversar los detalles.</p></div>
                    <div class="mt-6"><a data-cake-whatsapp target="_blank" rel="noopener" class="block w-full rounded-xl bg-blush px-4 py-3 text-center text-xs font-bold uppercase tracking-[0.1em] text-bordo transition hover:bg-white">Compartir diseño por WhatsApp</a></div>
                </aside>
            </div>

            <script type="application/json" data-cake-options>@json($customizerOptions)</script>
        </div>
    </div>
</section>
