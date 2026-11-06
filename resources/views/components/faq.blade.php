<section id="preguntas" class="scroll-mt-28 px-4 pb-20 pt-32 sm:px-6 sm:pb-28 sm:pt-40 lg:px-8">
    <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.7fr_1.3fr] lg:gap-24">
        <div class="reveal">
            <p class="section-kicker">Antes de encargar</p>
            <h1 class="mt-4 font-display text-4xl leading-tight text-bordo sm:text-5xl">Preguntas<br><span class="font-script text-6xl font-normal text-rosa-viejo">frecuentes</span></h1>
            <p class="mt-5 max-w-sm text-sm leading-7 text-ink/60">Si no encontrás lo que buscás, escribinos. Estamos para ayudarte a que todo salga lindo.</p>
        </div>
        <div class="divide-y divide-bordo/10">
            @foreach ([
                ['¿Cómo se reserva una fecha?', 'Trabajamos por encargo y confirmamos la fecha con una seña del 50% del total. El saldo se abona al recibir el pedido.'],
                ['¿Con cuánta anticipación tengo que pedir?', 'Cookies con 24 h, cupcakes y boxes con 48 h, tortas con 72 h y mesas dulces con 10 días. Las tortas personalizadas necesitan 7 días.'],
                ['¿A qué zonas hacen envíos?', 'Coordinamos envíos en zonas cercanas a Jujuy 422. El costo depende de la distancia y se informa antes de confirmar.'],
                ['¿Puedo retirar mi pedido?', 'Sí, podés retirarlo por Jujuy 422 en la franja horaria que elegiste, con reserva previa.'],
                ['¿Qué pasa si necesito cancelar?', 'Avisanos con la mayor anticipación posible. Para revisar cambios o cancelaciones, escribinos por WhatsApp con tu número de pedido.'],
                ['¿Tienen opciones sin alérgenos?', 'Todos nuestros productos se elaboran en el mismo obrador. Consultanos por ingredientes y posibles trazas antes de encargar.'],
                ['¿Cómo conservo lo que compré?', 'Cada producto incluye sus indicaciones. En general, las tortas y cupcakes van en heladera y las cookies en recipiente hermético.'],
                ['¿Qué formas de pago aceptan?', 'Transferencia, Mercado Pago o efectivo. En esta demo los pagos son simulados y no se realiza ningún cobro.'],
            ] as $faq)
                <div x-data="{ expanded: false }" class="py-5">
                    <button type="button" @click="expanded = !expanded" class="flex w-full items-center justify-between gap-5 text-left" :aria-expanded="expanded.toString()">
                        <span class="font-display text-lg text-bordo sm:text-xl">{{ $faq[0] }}</span>
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-blush text-xl leading-none text-bordo transition" :class="expanded ? 'rotate-45' : ''">+</span>
                    </button>
                    <div x-show="expanded" x-transition x-cloak class="max-w-2xl pr-12 pt-3 text-sm leading-6 text-ink/60">{{ $faq[1] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
