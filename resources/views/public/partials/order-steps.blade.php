<div class="reveal mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([
        ['01', 'Pilih Produk', 'Lihat katalog, baca detail produk dan profil penjual.'],
        ['02', 'Pesan via WhatsApp', 'Tekan tombol "Pesan via WhatsApp". Pesan awal sudah disiapkan otomatis.'],
        ['03', 'Sepakati & Bayar', 'Sepakati jumlah, harga akhir, ongkir, lalu bayar langsung kepada penjual.'],
        ['04', 'Produk Diterima', 'Penjual mengonfirmasi pembayaran, lalu produk diantar atau diambil di desa.'],
    ] as [$number, $title, $text])
        <div class="rounded-2xl border border-harvest-100 bg-white p-6">
            <div class="font-display text-3xl font-extrabold text-harvest-500">{{ $number }}</div>
            <h4 class="mt-3 font-display text-sm font-bold text-forest-700">{{ $title }}</h4>
            <p class="mt-1 text-xs text-forest-500">{{ $text }}</p>
        </div>
    @endforeach
</div>
