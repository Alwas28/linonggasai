{{-- Category bars + top products table. One hue for magnitude; values in text ink. --}}
@php
    $rupiah = fn (float $value): string => 'Rp '.number_format($value, 0, ',', '.');
    $categoryMax = max(1.0, (float) $categories->max('revenue'));
@endphp

<div class="grid gap-4 lg:grid-cols-5">
    <div class="card p-5 lg:col-span-2">
        <h3 class="font-display font-bold">Penjualan per Kategori</h3>
        @if ($categories->isEmpty())
            <p class="mt-4 text-sm text-forest-500">Belum ada data.</p>
        @else
            <ul class="mt-4 space-y-4 text-sm">
                @foreach ($categories as $category)
                    <li>
                        <div class="flex justify-between gap-2 font-semibold">
                            <span>{{ $category->icon }} {{ $category->name }}</span>
                            <span>{{ $rupiah((float) $category->revenue) }}</span>
                        </div>
                        <div class="mt-1.5 h-2.5 rounded-full bg-forest-50" title="{{ $category->name }}: {{ $rupiah((float) $category->revenue) }} · {{ $category->quantity }} unit">
                            <div class="h-full rounded-full bg-forest-500" style="width: {{ max(2, (float) $category->revenue / $categoryMax * 100) }}%"></div>
                        </div>
                        <div class="mt-1 text-[11px] text-forest-400">{{ number_format((int) $category->quantity, 0, ',', '.') }} unit</div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="card overflow-hidden lg:col-span-3">
        <div class="px-5 pt-4 font-display font-bold">Produk Terlaris</div>
        @if ($products->isEmpty())
            <p class="px-5 py-4 text-sm text-forest-500">Belum ada data.</p>
        @else
            <div class="overflow-x-auto">
                <table class="mt-3 w-full text-sm">
                    <thead class="bg-forest-50 text-left text-xs uppercase tracking-wide text-forest-500">
                        <tr>
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">Produk</th>
                            <th class="px-5 py-3 text-right">Terjual</th>
                            <th class="px-5 py-3 text-right">Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-forest-100">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-5 py-3 font-bold text-forest-400">{{ $loop->iteration }}</td>
                                <td class="px-5 py-3">
                                    <div class="font-semibold">{{ $product->product_name }}</div>
                                    @if ($showStore ?? false)
                                        <div class="text-xs text-forest-500">{{ $product->store_name }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">{{ number_format((int) $product->quantity, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right font-bold">{{ $rupiah((float) $product->revenue) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
