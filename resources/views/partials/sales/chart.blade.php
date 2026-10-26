{{--
    Single-series column chart (one hue, one axis). Each column has a hover/focus
    tooltip; only the peak is direct-labelled. The tables below are the table view.
    Expects $timeline: list of ['label' => string, 'revenue' => float, 'orders' => int].
--}}
@php
    $max = max(1.0, (float) $timeline->max('revenue'));
    $peakIndex = $timeline->search(fn (array $point): bool => $point['revenue'] === (float) $timeline->max('revenue') && $point['revenue'] > 0);
    $count = $timeline->count();
    $labelEvery = (int) max(1, ceil($count / 10));
    $shortRupiah = function (float $value): string {
        return match (true) {
            $value >= 1_000_000 => number_format($value / 1_000_000, 1, ',', '.').' jt',
            $value >= 1_000 => number_format($value / 1_000, 0, ',', '.').' rb',
            default => number_format($value, 0, ',', '.'),
        };
    };
@endphp

<div class="card p-5">
    <div class="flex items-center justify-between gap-3">
        <h3 class="font-display font-bold">{{ $title ?? 'Penjualan' }}</h3>
        <span class="text-xs text-forest-500">Rupiah · pesanan selesai</span>
    </div>

    @if ($timeline->sum('revenue') <= 0)
        <div class="mt-5 grid h-44 place-items-center rounded-xl bg-forest-50 text-sm text-forest-500">Belum ada penjualan pada periode ini.</div>
    @else
        <div class="relative mt-6 h-44">
            {{-- Recessive gridlines --}}
            <div class="pointer-events-none absolute inset-0 flex flex-col justify-between" aria-hidden="true">
                <div class="border-t border-dashed border-forest-100"></div>
                <div class="border-t border-dashed border-forest-100"></div>
                <div class="border-t border-forest-200"></div>
            </div>
            <div class="absolute -top-4 left-0 text-[10px] font-semibold text-forest-400">{{ $shortRupiah($max) }}</div>

            <ol class="relative flex h-full items-end gap-[2px]" aria-label="{{ $title ?? 'Penjualan' }}">
                @foreach ($timeline as $index => $point)
                    @php($height = $point['revenue'] > 0 ? max(2, $point['revenue'] / $max * 100) : 0)
                    <li tabindex="0" class="group relative flex h-full flex-1 items-end justify-center outline-none"
                        aria-label="{{ $point['label'] }}: Rp {{ number_format($point['revenue'], 0, ',', '.') }}, {{ $point['orders'] }} pesanan">
                        @if ($index === $peakIndex)
                            <span class="absolute z-10 whitespace-nowrap text-[10px] font-bold text-forest-700" style="bottom: calc({{ $height }}% + 4px)">{{ $shortRupiah($point['revenue']) }}</span>
                        @endif
                        <span class="block w-full max-w-[28px] rounded-t bg-forest-400 transition group-hover:bg-forest-600 group-focus:bg-forest-600" style="height: {{ $height }}%"></span>
                        {{-- Tooltip --}}
                        <span class="pointer-events-none absolute bottom-full z-20 mb-2 hidden whitespace-nowrap rounded-lg bg-forest-700 px-3 py-2 text-xs text-cream shadow-lg group-hover:block group-focus:block">
                            <span class="block font-bold">{{ $point['label'] }}</span>
                            Rp {{ number_format($point['revenue'], 0, ',', '.') }} · {{ $point['orders'] }} pesanan
                        </span>
                    </li>
                @endforeach
            </ol>
        </div>
        <div class="mt-2 flex gap-[2px] text-[10px] font-semibold text-forest-500" aria-hidden="true">
            @foreach ($timeline as $index => $point)
                <span class="flex-1 truncate text-center">{{ $index % $labelEvery === 0 ? $point['label'] : '' }}</span>
            @endforeach
        </div>
    @endif
</div>
