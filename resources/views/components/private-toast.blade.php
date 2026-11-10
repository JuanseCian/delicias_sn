@php
    $type = session('success') ? 'success' : (session('error') || $errors->any() ? 'error' : null);
    $message = session('success') ?? session('error') ?? $errors->first();
@endphp

@if ($message)
    <div
        x-data="{ visible: true }"
        x-init="setTimeout(() => visible = false, 5000)"
        x-show="visible"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-x-2 sm:translate-y-0"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="translate-y-2 opacity-0 sm:translate-x-2 sm:translate-y-0"
        class="fixed inset-x-4 top-4 z-[60] sm:left-auto sm:w-full sm:max-w-sm"
        role="{{ $type === 'error' ? 'alert' : 'status' }}"
        aria-live="polite"
    >
        <div class="flex items-start gap-3 rounded-2xl border p-4 shadow-[0_16px_40px_rgba(59,42,42,0.16)] {{ $type === 'error' ? 'border-[#E3B2A8] bg-[#FFF1EE] text-[#8A3C2E]' : 'border-[#A5C8A8] bg-[#EEF8EF] text-[#315D38]' }}">
            <span class="mt-0.5 grid h-7 w-7 shrink-0 place-items-center rounded-full {{ $type === 'error' ? 'bg-[#F6D7D0]' : 'bg-[#D8ECD9]' }}" aria-hidden="true">
                @if ($type === 'error')
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 8v4m0 4h.01"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg>
                @else
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                @endif
            </span>
            <p class="flex-1 pt-1 text-sm font-semibold leading-5">{{ $message }}</p>
            <button type="button" @click="visible = false" class="rounded-full p-1 opacity-60 transition hover:bg-black/5 hover:opacity-100" aria-label="Cerrar aviso">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </div>
@endif
