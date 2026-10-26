@extends('layouts.seller')

@section('title', 'Rekap Pesanan')

@use('App\Enums\OrderStatus')

@section('content')
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-2xl font-extrabold">Rekap Pesanan</h1>
            <p class="text-sm text-forest-500">Catatan opsional untuk pesanan yang disepakati di WhatsApp.</p>
        </div>
        <a href="{{ route('seller.orders.create') }}" class="btn-primary">+ Catat</a>
    </div>

    <div class="no-scrollbar -mx-4 mt-4 flex gap-2 overflow-x-auto px-4">
        <a href="{{ route('seller.orders.index') }}" class="chip {{ $tab === 'aktif' ? 'chip-active' : '' }}">Aktif</a>
        @foreach (OrderStatus::cases() as $status)
            <a href="{{ route('seller.orders.index', ['tab' => $status->value]) }}" class="chip {{ $tab === $status->value ? 'chip-active' : '' }}">{{ $status->label() }}</a>
        @endforeach
        <a href="{{ route('seller.orders.index', ['tab' => 'semua']) }}" class="chip {{ $tab === 'semua' ? 'chip-active' : '' }}">Semua</a>
    </div>

    @if ($orders->isEmpty())
        <x-empty-state class="mt-6" icon="🧺" title="Belum ada pesanan di sini">
            Pesanan masuk melalui WhatsApp. Catat di sini bila Anda ingin menyimpan rekapnya.
        </x-empty-state>
    @else
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            @foreach ($orders as $order)
                @include('seller.orders.partials.card', ['order' => $order])
            @endforeach
        </div>
        {{ $orders->links() }}
    @endif
@endsection
