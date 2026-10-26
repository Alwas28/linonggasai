@extends('layouts.admin')

@section('title', 'Halaman Informasi')

@section('content')
    <p class="text-sm text-forest-500">Konten halaman publik Tentang, Cara Pesan, Lokasi, dan Kontak.</p>
    <div class="mt-5 grid gap-4 sm:grid-cols-2">
        @foreach ($pages as $page)
            <div class="card p-5">
                <div class="text-xs font-bold uppercase tracking-widest text-harvest-600">/{{ $page->slug }}</div>
                <h3 class="mt-1 font-display text-lg font-bold">{{ $page->title }}</h3>
                <p class="mt-1 line-clamp-3 text-sm text-forest-500">{{ $page->content }}</p>
                <div class="mt-3 flex items-center justify-between text-xs font-bold">
                    <span class="text-forest-400">Diubah {{ $page->updated_at->diffForHumans() }}</span>
                    <a href="{{ route('admin.pages.edit', $page) }}" class="text-harvest-600">Edit →</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
