<section id="preguntas" class="scroll-mt-28 px-4 pb-20 pt-32 sm:px-6 sm:pb-28 sm:pt-40 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.7fr_1.3fr] lg:gap-24">
        <div class="reveal">
            <p class="section-kicker">Antes de encargar</p>
            <h1 class="mt-4 font-display text-4xl leading-tight text-bordo sm:text-5xl">Preguntas<br><span class="font-script text-6xl font-normal text-rosa-viejo">frecuentes</span></h1>
            <p class="mt-5 max-w-sm text-sm leading-7 text-ink/60">Si no encontrás lo que buscás, escribinos. Estamos para ayudarte a que todo salga lindo.</p>
        </div>
        <div class="divide-y divide-bordo/10">
            @foreach ($faqs as $faq)
                <div x-data="{ expanded: false }" class="py-5">
                    <button type="button" @click="expanded = !expanded" class="flex w-full items-center justify-between gap-5 text-left" :aria-expanded="expanded.toString()">
                        <span class="font-display text-lg text-bordo sm:text-xl">{{ $faq['question'] }}</span>
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-blush text-xl leading-none text-bordo transition" :class="expanded ? 'rotate-45' : ''">+</span>
                    </button>
                    <div x-show="expanded" x-transition x-cloak class="max-w-2xl pr-12 pt-3 text-sm leading-6 text-ink/60">{{ $faq['answer'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
