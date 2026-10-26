@extends('layouts.admin')

@section('title', 'Penjual')

@use('App\Enums\VerificationStatus')

@section('content')
    <div class="flex flex-wrap items-center gap-3">
        <div class="no-scrollbar flex gap-2 overflow-x-auto">
            <a href="{{ route('admin.sellers.index', array_filter(['q' => $search])) }}" class="chip {{ $activeStatus ? '' : 'chip-active' }}">Semua</a>
            @foreach (VerificationStatus::cases() as $status)
                <a href="{{ route('admin.sellers.index', array_filter(['status' => $status->value, 'q' => $search])) }}" class="chip {{ $activeStatus === $status ? 'chip-active' : '' }}">{{ $status->label() }}</a>
            @endforeach
        </div>
        <form method="GET" class="ml-auto flex w-full items-center rounded-full border border-forest-100 bg-white px-4 py-2 sm:w-64">
            @if ($activeStatus)
                <input type="hidden" name="status" value="{{ $activeStatus->value }}">
            @endif
            <label for="q" class="sr-only">Cari penjual</label>
            <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Cari nama, email, toko…" class="w-full border-0 bg-transparent p-0 text-sm placeholder:text-forest-400 focus:ring-0">
        </form>
    </div>

    @if ($sellers->isEmpty())
        <x-empty-state class="mt-5" icon="👥" title="Tidak ada penjual" />
    @else
        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($sellers as $seller)
                <a href="{{ route('admin.sellers.show', $seller) }}" class="card flex items-center gap-3 p-4 transition hover:border-harvest-300">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-forest-100 font-display font-bold">{{ $seller->initials() }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate font-bold">{{ $seller->name }}</div>
                        <div class="truncate text-xs text-forest-500">{{ $seller->store?->name ?? '-' }} · {{ $seller->store?->published_products_count ?? 0 }} produk tayang</div>
                    </div>
                    @if ($seller->sellerProfile)
                        <x-badge :status="$seller->sellerProfile->verification_status" />
                    @endif
                </a>
            @endforeach
        </div>
        {{ $sellers->links() }}
    @endif
@endsection
