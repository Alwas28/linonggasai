@extends('layouts.public')

@section('title', $page?->title ?? 'Tentang L-DCH')

@section('content')
    @include('public.partials.page-header', ['kicker' => 'Tentang Kami', 'fallbackTitle' => 'Tentang L-DCH Linonggasai'])

    <section class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['🏛️', 'Pengelola', $settings['manager_name']],
                ['🌾', 'Identitas Pendukung', 'BUMDes Meohai'],
                ['📍', 'Wilayah', 'Desa Linonggasai, Kec. Wonggeduku Barat, Kab. Konawe'],
                ['🛒', 'Kategori', 'Pertanian, Perikanan, dan UMKM'],
            ] as [$icon, $label, $value])
                <div class="card p-5">
                    <div class="text-2xl" aria-hidden="true">{{ $icon }}</div>
                    <div class="mt-2 text-xs font-bold uppercase tracking-widest text-harvest-600">{{ $label }}</div>
                    <div class="mt-1 font-display font-bold">{{ $value }}</div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
