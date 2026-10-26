@extends('layouts.admin')

@section('title', 'Banner Beranda')

@section('content')
    <div class="flex items-center justify-between gap-3">
        <p class="text-sm text-forest-500">Maksimal 3 banner aktif ditampilkan di beranda, diurutkan berdasarkan urutan.</p>
        <a href="{{ route('admin.banners.create') }}" class="btn-primary shrink-0">+ Banner</a>
    </div>

    @if ($banners->isEmpty())
        <x-empty-state class="mt-5" icon="🖼️" title="Belum ada banner">Banner promosi akan tampil di bawah hero beranda.</x-empty-state>
    @else
        <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($banners as $banner)
                <div class="card overflow-hidden {{ $banner->is_active ? '' : 'opacity-60' }}">
                    <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->title }}" class="aspect-[16/9] w-full object-cover">
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-display font-bold">{{ $banner->title }}</h3>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $banner->is_active ? 'bg-forest-100 text-forest-700' : 'bg-forest-50 text-forest-500' }}">{{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                        <p class="mt-1 text-xs text-forest-500">{{ $banner->subtitle }}</p>
                        <div class="mt-3 flex items-center justify-between text-xs font-bold">
                            <span class="text-forest-400">Urutan {{ $banner->sort_order }}</span>
                            <div class="flex gap-3">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="text-harvest-600">Edit</a>
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" data-confirm="Hapus banner ini?">
                                    @csrf @method('DELETE')
                                    <button class="text-clay-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
