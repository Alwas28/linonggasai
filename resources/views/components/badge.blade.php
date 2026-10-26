@props(['status'])

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-bold '.$status->badgeClasses()]) }}>
    {{ $slot->isEmpty() ? $status->label() : $slot }}
</span>
