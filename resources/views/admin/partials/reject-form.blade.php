{{-- Inline "reject with reason" form; reasons are always recorded (blueprint 8.3). --}}
<details class="group {{ $class ?? 'flex-1' }}">
    <summary class="flex cursor-pointer list-none items-center justify-center rounded-full border border-clay-400 py-2 text-xs font-bold text-clay-600 transition hover:bg-clay-50">{{ $label ?? 'Tolak' }}</summary>
    <form method="POST" action="{{ $action }}" class="mt-2 space-y-2">
        @csrf @method('PATCH')
        <label class="sr-only" for="note-{{ $id }}">Alasan</label>
        <textarea id="note-{{ $id }}" name="note" rows="2" required maxlength="1000" class="form-input text-xs" placeholder="{{ $placeholder ?? 'Tuliskan alasan agar penjual dapat memperbaiki…' }}"></textarea>
        <button class="w-full rounded-full bg-clay-600 py-2 text-xs font-bold text-white">{{ $submit ?? 'Kirim Penolakan' }}</button>
    </form>
</details>
