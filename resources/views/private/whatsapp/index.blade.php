@extends('layouts.private')

@section('title', 'WhatsApp · Delicias Sn')

@php
    $connectionState = data_get($connection, 'instance.state', 'desconocido');
    $connectionLabels = ['open' => 'Conectado', 'close' => 'Desconectado', 'connecting' => 'Conectando'];
    $messageLabels = ['queued' => 'En cola', 'sending' => 'Enviando', 'retrying' => 'Reintentando', 'sent' => 'Enviado', 'delivered' => 'Entregado', 'failed' => 'Fallido'];
@endphp

@section('content')
<main class="min-h-screen bg-crema px-4 py-6 text-ink sm:px-6 lg:px-10" x-data="{ tab: 'conexion', qr: null, qrError: null, async loadQr() { this.qrError = null; try { const response = await fetch('{{ route('private.whatsapp.qr') }}', { headers: { Accept: 'application/json' } }); const payload = await response.json(); if (!response.ok) throw new Error(payload.message || 'No se pudo obtener el QR.'); this.qr = payload.base64 || payload.qrcode?.base64 || payload.qr || null; if (!this.qr) this.qrError = 'Evolution no devolvió un código QR nuevo.'; } catch (error) { this.qrError = error.message; } } }">
    <div class="mx-auto max-w-6xl">
        <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#64747A] hover:text-[#263238]">← Volver al panel</a>
                <p class="mt-5 text-xs font-bold uppercase tracking-[0.14em] text-[#64747A]">Centro de comunicación</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#263238] sm:text-4xl">WhatsApp</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#263238]/60">Evolution API, automatizaciones, plantillas y trazabilidad de cada mensaje.</p>
            </div>
            <div class="rounded-2xl border border-[#263238]/10 bg-white px-4 py-3 text-sm">
                <span class="mr-2 inline-block h-2.5 w-2.5 rounded-full {{ $connectionState === 'open' ? 'bg-[#35B96B]' : 'bg-[#C68B59]' }}"></span>
                <strong>{{ $connectionLabels[$connectionState] ?? ucfirst($connectionState) }}</strong>
                <span class="ml-2 text-[#263238]/50">{{ config('services.evolution.instance') }}</span>
            </div>
        </header>

        @if (session('status'))<p class="mt-6 rounded-xl bg-[#EEF8EF] px-4 py-3 text-sm font-semibold text-[#315D38]" role="status">{{ session('status') }}</p>@endif
        @if ($errors->any())<div class="mt-6 rounded-xl bg-[#FFF1EE] px-4 py-3 text-sm font-semibold text-[#8A3C2E]" role="alert">{{ $errors->first() }}</div>@endif

        <div class="mt-8 flex flex-wrap gap-2 rounded-2xl border border-[#263238]/10 bg-white p-2" role="tablist">
            @foreach (['conexion' => 'Conexión', 'automatizaciones' => 'Automatizaciones', 'plantillas' => 'Plantillas', 'historial' => 'Historial'] as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'bg-[#6B2D3C] text-white' : 'text-[#64747A] hover:bg-[#F5F1EC]'" class="rounded-xl px-4 py-2.5 text-xs font-bold uppercase tracking-[0.08em]">{{ $label }}</button>
            @endforeach
        </div>

        <section x-show="tab === 'conexion'" x-cloak class="mt-6 grid gap-5 lg:grid-cols-[1fr_1fr]">
            <article class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,50,0.06)]">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#64747A]">Estado de la instancia</p>
                <h2 class="mt-2 text-2xl font-bold text-[#263238]">{{ $connectionLabels[$connectionState] ?? ucfirst($connectionState) }}</h2>
                @if ($connectionError)<p class="mt-3 text-sm leading-6 text-[#8A3C2E]">{{ $connectionError }}</p>@else<p class="mt-3 text-sm leading-6 text-[#263238]/60">Instancia <strong>{{ config('services.evolution.instance') }}</strong> en <strong>{{ config('services.evolution.base_url') }}</strong>.</p>@endif
                <div class="mt-6 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('private.whatsapp.connect') }}">@csrf<button class="rounded-xl bg-[#6B2D3C] px-4 py-3 text-sm font-bold text-white">Conectar / generar QR</button></form>
                    <button type="button" @click="loadQr()" class="rounded-xl bg-[#E9EFF0] px-4 py-3 text-sm font-bold text-[#263238]">Consultar QR</button>
                    <form method="POST" action="{{ route('private.whatsapp.logout') }}">@csrf @method('DELETE')<button class="rounded-xl border border-[#8A3C2E]/20 px-4 py-3 text-sm font-bold text-[#8A3C2E]">Desconectar</button></form>
                </div>
                <div x-show="qr" class="mt-6 rounded-2xl bg-[#F5F1EC] p-5 text-center"><img :src="qr" alt="Código QR de conexión de WhatsApp" class="mx-auto max-h-64 w-auto"><p class="mt-3 text-xs text-[#64747A]">Escanealo desde WhatsApp → Dispositivos vinculados.</p></div>
                <p x-show="qrError" x-text="qrError" class="mt-4 text-sm font-semibold text-[#8A3C2E]"></p>
            </article>
            <article class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,50,0.06)]">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#64747A]">Seguridad operativa</p>
                <h2 class="mt-2 text-2xl font-bold text-[#263238]">Automatización global</h2>
                <p class="mt-3 text-sm leading-6 text-[#263238]/60">Cuando está desactivada, los pedidos nunca generan envíos automáticos. Los mensajes manuales de prueba siguen quedando registrados en la cola.</p>
                <form method="POST" action="{{ route('private.whatsapp.toggle') }}" class="mt-7 flex items-center justify-between rounded-2xl bg-[#F5F1EC] p-4">@csrf @method('PATCH')<div><p class="font-bold text-[#263238]">{{ $globalEnabled ? 'Habilitada' : 'Deshabilitada' }}</p><p class="text-xs text-[#64747A]">WHATSAPP_ENABLED / ajuste del panel</p></div><input type="hidden" name="enabled" value="{{ $globalEnabled ? 0 : 1 }}"><button class="rounded-xl {{ $globalEnabled ? 'bg-[#C68B59]' : 'bg-[#1B7F45]' }} px-4 py-2.5 text-sm font-bold text-white">{{ $globalEnabled ? 'Desactivar' : 'Activar' }}</button></form>
                <dl class="mt-6 grid gap-3 text-sm"><div class="flex justify-between border-b border-[#263238]/10 pb-3"><dt class="text-[#64747A]">Proveedor</dt><dd class="font-bold text-[#263238]">Evolution API</dd></div><div class="flex justify-between border-b border-[#263238]/10 pb-3"><dt class="text-[#64747A]">Cola</dt><dd class="font-bold text-[#263238]">whatsapp</dd></div><div class="flex justify-between"><dt class="text-[#64747A]">Webhook</dt><dd class="font-bold text-[#1B7F45]">Idempotente</dd></div></dl>
            </article>
        </section>

        <section x-show="tab === 'automatizaciones'" x-cloak class="mt-6 grid gap-5">
            @foreach ($automations as $automation)
                <form method="POST" action="{{ route('private.whatsapp.automations.update', $automation) }}" class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,50,0.06)]">@csrf @method('PATCH')<div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-start"><div><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#64747A]">{{ $automation->trigger }}</p><h2 class="mt-2 text-2xl font-bold text-[#263238]">{{ $automation->name }}</h2><p class="mt-2 text-sm text-[#263238]/60">Se dispara cuando el pedido cambia a entregado y existe consentimiento.</p></div><label class="flex items-center gap-3 text-sm font-bold text-[#263238]"><input type="hidden" name="is_enabled" value="0"><input type="checkbox" name="is_enabled" value="1" @checked($automation->is_enabled) class="h-5 w-5 rounded border-[#263238]/20 text-[#6B2D3C]"> Activa</label></div><div class="mt-6 grid gap-4 md:grid-cols-2"><label class="text-xs font-bold uppercase tracking-[0.1em] text-[#64747A]">Plantilla<select name="template_id" class="mt-2 w-full rounded-xl border border-[#263238]/15 bg-[#F5F1EC] px-3 py-3 text-sm font-normal text-[#263238]">@foreach ($templates as $template)<option value="{{ $template->id }}" @selected($automation->template_id === $template->id)>{{ $template->name }}</option>@endforeach</select></label><label class="text-xs font-bold uppercase tracking-[0.1em] text-[#64747A]">Demora (minutos)<input type="number" name="delay_minutes" min="0" max="10080" value="{{ $automation->delay_minutes }}" class="mt-2 w-full rounded-xl border border-[#263238]/15 bg-[#F5F1EC] px-3 py-3 text-sm font-normal text-[#263238]"></label></div><button class="mt-5 rounded-xl bg-[#263238] px-4 py-3 text-sm font-bold text-white">Guardar automatización</button></form>
            @endforeach
        </section>

        <section x-show="tab === 'plantillas'" x-cloak class="mt-6 grid gap-5 lg:grid-cols-2">
            @foreach ($templates as $template)
                @php
                    $variableOpen = chr(123).chr(123);
                    $variableClose = chr(125).chr(125);
                    $previewBody = strtr($template->body, [
                        $variableOpen.'nombre'.$variableClose => 'Sofía',
                        $variableOpen.'codigo'.$variableClose => 'DSN-2026-0001',
                        $variableOpen.'enlace_resena'.$variableClose => 'https://deliciassn.com/pedidos/valorar/ejemplo',
                        $variableOpen.'fecha_entrega'.$variableClose => '28/11/2026',
                    ]);
                @endphp
                <form method="POST" action="{{ route('private.whatsapp.templates.update', $template) }}" class="rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,50,0.06)]">@csrf @method('PUT')<div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#64747A]">{{ $template->key }}</p><h2 class="mt-2 text-xl font-bold text-[#263238]">{{ $template->name }}</h2></div><label class="flex items-center gap-2 text-xs font-bold text-[#64747A]"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked($template->is_active) class="h-4 w-4 rounded text-[#6B2D3C]"> Activa</label></div><textarea name="body" rows="6" class="mt-5 w-full rounded-xl border border-[#263238]/15 bg-[#F5F1EC] px-4 py-3 text-sm leading-6 text-[#263238]">{{ $template->body }}</textarea><p class="mt-3 text-xs leading-5 text-[#64747A]">Variables disponibles: <code>&#123;&#123;nombre&#125;&#125;</code> <code>&#123;&#123;codigo&#125;&#125;</code> <code>&#123;&#123;enlace_resena&#125;&#125;</code> <code>&#123;&#123;fecha_entrega&#125;&#125;</code></p><div class="mt-5 rounded-2xl bg-[#F5F1EC] p-4"><p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#64747A]">Vista previa</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-[#263238]">{{ $previewBody }}</p></div><button class="mt-5 rounded-xl bg-[#263238] px-4 py-3 text-sm font-bold text-white">Guardar plantilla</button></form>
            @endforeach
        </section>

        <section x-show="tab === 'historial'" x-cloak class="mt-6">
                <form method="POST" action="{{ route('private.whatsapp.test') }}" class="grid gap-3 rounded-[1.5rem] border border-[#263238]/10 bg-white p-6 shadow-[0_8px_24px_rgba(38,50,50,0.06)] md:grid-cols-[.4fr_1fr_auto] md:items-end">@csrf<label class="text-xs font-bold uppercase tracking-[0.1em] text-[#64747A]">Destino<input name="recipient" required class="mt-2 w-full rounded-xl border border-[#263238]/15 bg-[#F5F1EC] px-3 py-3 text-sm font-normal" placeholder="11 1234 5678"></label><label class="text-xs font-bold uppercase tracking-[0.1em] text-[#64747A]">Mensaje de prueba<textarea name="body" required rows="2" class="mt-2 w-full rounded-xl border border-[#263238]/15 bg-[#F5F1EC] px-3 py-3 text-sm font-normal">Mensaje de prueba desde Delicias Sn.</textarea></label><button class="rounded-xl bg-[#1B7F45] px-4 py-3 text-sm font-bold text-white">Encolar prueba</button></form>
            <form method="GET" action="{{ route('private.whatsapp.index') }}" class="mt-5 grid gap-3 rounded-[1.5rem] border border-[#263238]/10 bg-white p-5 md:grid-cols-[1fr_auto_auto] md:items-end"><label class="text-xs font-bold uppercase tracking-[0.1em] text-[#64747A]">Buscar pedido, ID o teléfono<input name="search" value="{{ request('search') }}" class="mt-2 w-full rounded-xl border border-[#263238]/15 bg-[#F5F1EC] px-3 py-3 text-sm font-normal" placeholder="DSN-2026, 549..." /></label><label class="text-xs font-bold uppercase tracking-[0.1em] text-[#64747A]">Estado<select name="status" class="mt-2 w-full rounded-xl border border-[#263238]/15 bg-[#F5F1EC] px-3 py-3 text-sm font-normal"><option value="">Todos</option>@foreach ($messageLabels as $status => $label)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>@endforeach</select></label><button class="rounded-xl bg-[#263238] px-4 py-3 text-sm font-bold text-white">Filtrar</button></form>
            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-[#263238]/10 bg-white shadow-[0_8px_24px_rgba(38,50,50,0.06)]"><div class="overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="bg-[#F5F1EC] text-xs uppercase tracking-[0.1em] text-[#64747A]"><tr><th class="px-5 py-4">Fecha</th><th class="px-5 py-4">Destino</th><th class="px-5 py-4">Automatización</th><th class="px-5 py-4">Estado</th><th class="px-5 py-4">Acción</th></tr></thead><tbody class="divide-y divide-[#263238]/10">@forelse ($messages as $message)<tr><td class="whitespace-nowrap px-5 py-4 text-[#64747A]">{{ $message->created_at->format('d/m/Y H:i') }}</td><td class="px-5 py-4 font-semibold text-[#263238]">{{ $message->recipient }}</td><td class="px-5 py-4 text-[#263238]/60">{{ $message->automation_key ?: 'Manual' }}</td><td class="px-5 py-4"><span class="rounded-full bg-[#E9EFF0] px-3 py-1.5 text-xs font-bold">{{ $messageLabels[$message->status] ?? $message->status }}</span>@if($message->error_message)<p class="mt-1 max-w-xs text-[#8A3C2E]">{{ $message->error_message }}</p>@endif</td><td class="px-5 py-4">@if($message->status === 'failed')<form method="POST" action="{{ route('private.whatsapp.messages.retry', $message) }}">@csrf<button class="font-bold text-[#6B2D3C] hover:underline">Reintentar</button></form>@else<span class="text-[#64747A]">—</span>@endif</td></tr>@empty<tr><td colspan="5" class="px-5 py-10 text-center text-[#64747A]">Todavía no hay mensajes registrados.</td></tr>@endforelse</tbody></table></div><div class="border-t border-[#263238]/10 px-5 py-4">{{ $messages->links() }}</div></div>
        </section>
    </div>
</main>
@endsection

@push('head')
<style>[x-cloak]{display:none!important}</style>
@endpush
