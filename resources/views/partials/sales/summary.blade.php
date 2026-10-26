@php($rupiah = fn (float $value): string => 'Rp '.number_format($value, 0, ',', '.'))

<div class="mt-4 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
    <x-stat-card label="Total Penjualan" :value="$rupiah($summary['revenue'])" :note="$summary['orders'].' pesanan selesai'" />
    <x-stat-card label="Lunas Dikonfirmasi" :value="$rupiah($summary['confirmed_paid'])"
                 :note="$summary['unconfirmed_paid'] > 0 ? $rupiah($summary['unconfirmed_paid']).' belum dikonfirmasi' : 'Semua sudah dikonfirmasi'"
                 :note-class="$summary['unconfirmed_paid'] > 0 ? 'text-harvest-600' : 'text-forest-400'" />
    <x-stat-card label="Rata-rata per Pesanan" :value="$rupiah($summary['average'])" :note="number_format($summary['items'], 0, ',', '.').' unit terjual'" />
    <x-stat-card label="Pesanan Tercatat" :value="$summary['recorded']" :note="$summary['cancelled'].' dibatalkan'" :note-class="$summary['cancelled'] > 0 ? 'text-clay-500' : 'text-forest-400'" />
</div>
