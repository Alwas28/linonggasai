@extends('layouts.public')

@section('title', $page?->title ?? 'Kontak')

@section('content')
    @include('public.partials.page-header', ['kicker' => 'Hubungi Kami', 'fallbackTitle' => 'Kontak Pengelola'])

    <section class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="card p-6">
                <div class="text-2xl" aria-hidden="true">🏛️</div>
                <div class="mt-2 text-xs font-bold uppercase tracking-widest text-harvest-600">Pengelola</div>
                <div class="mt-1 font-display font-bold">{{ $settings['manager_name'] }}</div>
                <p class="mt-1 text-sm text-forest-500">{{ $settings['address'] }}</p>
            </div>
            <div class="card p-6">
                <div class="text-2xl" aria-hidden="true">💬</div>
                <div class="mt-2 text-xs font-bold uppercase tracking-widest text-harvest-600">WhatsApp Pengelola</div>
                @if ($settings['admin_whatsapp'])
                    <a href="{{ \App\Services\WhatsApp::link($settings['admin_whatsapp'], 'Halo pengelola L-DCH Linonggasai, saya ingin bertanya.') }}" target="_blank" rel="noopener" class="mt-1 block font-display font-bold text-harvest-600 hover:text-harvest-700">{{ \App\Services\WhatsApp::display($settings['admin_whatsapp']) }}</a>
                @else
                    <div class="mt-1 font-display font-bold text-forest-400">Belum tersedia</div>
                @endif
                <p class="mt-1 text-sm text-forest-500">Untuk pertanyaan seputar L-DCH dan pendaftaran penjual. Pemesanan produk langsung ke penjual.</p>
            </div>
            <div class="card p-6">
                <div class="text-2xl" aria-hidden="true">✉️</div>
                <div class="mt-2 text-xs font-bold uppercase tracking-widest text-harvest-600">Email</div>
                <div class="mt-1 font-display font-bold {{ $settings['contact_email'] ? '' : 'text-forest-400' }}">{{ $settings['contact_email'] ?: 'Belum tersedia' }}</div>
            </div>
        </div>
    </section>
@endsection
