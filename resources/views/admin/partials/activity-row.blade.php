<div class="flex items-start gap-3 py-3 text-sm">
    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-forest-50 font-display text-xs font-bold">{{ $log->user?->initials() ?? '—' }}</div>
    <div class="min-w-0 flex-1">
        <div><span class="font-bold">{{ $log->user?->name ?? 'Sistem' }}</span> <span class="text-forest-600">{{ mb_strtolower($log->description()) }}</span></div>
        @php($details = collect($log->metadata ?? [])->only(['seller', 'name', 'title', 'code', 'email', 'slug', 'note', 'status'])->filter())
        @if ($details->isNotEmpty())
            <div class="truncate text-xs text-forest-500">{{ $details->implode(' · ') }}</div>
        @endif
    </div>
    <time class="shrink-0 text-[11px] text-forest-400" datetime="{{ $log->created_at?->toIso8601String() }}" title="{{ $log->created_at?->translatedFormat('d F Y H:i') }}">{{ $log->created_at?->diffForHumans(short: true) }}</time>
</div>
