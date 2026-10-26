@extends('layouts.public')

@section('title', $page?->title ?? 'Cara Pesan')

@section('content')
    @include('public.partials.page-header', ['kicker' => 'Panduan', 'fallbackTitle' => 'Cara Pemesanan & Pembayaran'])

    <section class="mx-auto max-w-6xl px-6 py-12">
        @include('public.partials.order-steps')

        <div class="mt-10 grid gap-4 md:grid-cols-2">
            <div class="card p-6">
                <h2 class="font-display text-lg font-bold">💬 Contoh pesan WhatsApp</h2>
                <pre class="mt-3 whitespace-pre-wrap rounded-xl bg-forest-50 p-4 font-body text-xs leading-relaxed text-forest-600">Halo, saya tertarik memesan produk melalui L-DCH Linonggasai.

Nama produk: [Nama Produk]
Toko: [Nama Toko]
Harga yang tercantum: [Harga Produk]
Jumlah yang ingin dipesan: [Jumlah]

Mohon informasi ketersediaan produk, total pembayaran, metode pembayaran, serta pilihan pengambilan atau pengiriman. Terima kasih.</pre>
            </div>
            <div class="rounded-2xl border border-harvest-100 bg-harvest-50 p-6">
                <h2 class="font-display text-lg font-bold">⚠️ Penting tentang pembayaran</h2>
                <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-forest-600">
                    <li>Website L-DCH <strong>tidak memproses uang</strong> dan tidak menyediakan payment gateway.</li>
                    <li>Pembayaran dilakukan <strong>langsung kepada penjual</strong> melalui metode yang disepakati.</li>
                    <li>Pesanan dianggap lunas setelah <strong>penjual memeriksa dan mengonfirmasi</strong> penerimaan dana.</li>
                    <li>Menekan tombol WhatsApp <strong>belum berarti</strong> pesanan dibuat atau dibayar.</li>
                </ul>
            </div>
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('products.index') }}" class="btn-primary px-6 py-3.5">Mulai Belanja</a>
        </div>
    </section>
@endsection
