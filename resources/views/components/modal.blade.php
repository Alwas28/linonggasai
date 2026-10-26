@props(['id', 'title'])

{{-- Bottom sheet on mobile, centered dialog on desktop (reference: "Jual Produk" sheet). --}}
<div id="{{ $id }}" data-modal class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <div class="absolute inset-0 bg-forest-700/50" data-modal-close></div>
    <div class="absolute inset-x-0 bottom-0 max-h-[92vh] overflow-y-auto rounded-t-3xl bg-cream p-6 sm:inset-auto sm:left-1/2 sm:top-1/2 sm:w-[28rem] sm:-translate-x-1/2 sm:-translate-y-1/2 sm:rounded-3xl">
        <div class="mx-auto mb-4 h-1.5 w-10 rounded-full bg-forest-200 sm:hidden"></div>
        <h3 id="{{ $id }}-title" class="font-display text-lg font-extrabold">{{ $title }}</h3>
        {{ $slot }}
    </div>
</div>
