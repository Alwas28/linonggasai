@use('App\Services\SalesReport')

{{-- Period filter in one row above the charts; custom dates appear only for "Pilih tanggal". --}}
<form method="GET" action="{{ $action }}" class="card flex flex-wrap items-end gap-3 p-4">
    <div>
        <label for="periode" class="form-label">Periode</label>
        <select id="periode" name="periode" class="form-input" onchange="document.getElementById('custom-range').classList.toggle('hidden', this.value !== 'kustom')">
            @foreach (SalesReport::PERIODS as $value => $label)
                <option value="{{ $value }}" @selected($report->period === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div id="custom-range" class="{{ $report->period === 'kustom' ? '' : 'hidden' }} flex flex-wrap gap-3">
        <div>
            <label for="dari" class="form-label">Dari</label>
            <input id="dari" type="date" name="dari" value="{{ $report->from->format('Y-m-d') }}" class="form-input">
        </div>
        <div>
            <label for="sampai" class="form-label">Sampai</label>
            <input id="sampai" type="date" name="sampai" value="{{ $report->to->format('Y-m-d') }}" class="form-input">
        </div>
    </div>
    @isset($storeOptions)
        <div>
            <label for="toko" class="form-label">Toko</label>
            <select id="toko" name="toko" class="form-input">
                <option value="">Semua toko</option>
                @foreach ($storeOptions as $store)
                    <option value="{{ $store->id }}" @selected($report->storeId === $store->id)>{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
    @endisset
    <button class="btn-dark">Tampilkan</button>
    <a href="{{ $exportUrl }}" class="btn-outline ml-auto">⬇ Unduh CSV</a>
</form>
<p class="mt-2 text-xs text-forest-500">
    Periode <strong>{{ $report->label() }}</strong>. Penjualan dihitung dari pesanan berstatus <strong>Selesai</strong> yang dicatat penjual. Website tidak memproses pembayaran, jadi angka ini selengkap catatan penjual.
</p>
