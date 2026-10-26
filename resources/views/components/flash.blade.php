@php
    $message = session('success') ?? session('error') ?? session('status');
    $isError = session()->has('error');
@endphp

@if ($message)
    <div id="toast" role="status"
         class="fixed bottom-24 left-1/2 z-[70] flex w-[calc(100%-2rem)] max-w-md -translate-x-1/2 items-start gap-3 rounded-2xl px-5 py-3 text-sm font-semibold shadow-xl transition-opacity duration-500 lg:bottom-8 {{ $isError ? 'bg-clay-600 text-white' : 'bg-forest-700 text-cream' }}">
        <span aria-hidden="true">{{ $isError ? '⚠️' : '✓' }}</span>
        <span class="flex-1">{{ $message }}</span>
        <button type="button" data-dismiss class="opacity-70 hover:opacity-100" aria-label="Tutup">✕</button>
    </div>
@endif
