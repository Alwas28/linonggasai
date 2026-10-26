<div class="mb-4 flex gap-2">
    <a href="{{ route('admin.reports.sales') }}" class="chip {{ request()->routeIs('admin.reports.sales') ? 'chip-active' : '' }}">💰 Penjualan</a>
    <a href="{{ route('admin.reports') }}" class="chip {{ request()->routeIs('admin.reports') ? 'chip-active' : '' }}">📋 Penjual & Produk</a>
</div>
