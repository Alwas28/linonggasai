<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'tentang' => [
                'title' => 'Tentang L-DCH Linonggasai',
                'content' => "Linonggasai Digital Creative Hub (L-DCH) adalah pusat katalog dan pemasaran digital produk Desa Linonggasai yang dikelola oleh Karang Taruna Tunas Maju, dengan dukungan BUMDes Meohai.\n\nWebsite ini menjadi etalase digital terpusat untuk memperkenalkan hasil pertanian, perikanan, dan usaha mikro warga Desa Linonggasai, Kecamatan Wonggeduku Barat, Kabupaten Konawe, Sulawesi Tenggara.\n\nSetiap penjual adalah warga yang telah diverifikasi pengelola, dan setiap produk diperiksa sebelum tampil di katalog. Transaksi dilakukan langsung antara pembeli dan penjual melalui WhatsApp.",
            ],
            'cara-pesan' => [
                'title' => 'Cara Pemesanan & Pembayaran',
                'content' => "Pilih produk di katalog, lalu tekan tombol \"Pesan via WhatsApp\". WhatsApp akan terbuka dengan pesan awal yang sudah disiapkan.\n\nSepakati jumlah, ketersediaan, harga akhir, ongkos kirim, serta cara pengambilan atau pengiriman bersama penjual. Pembayaran dilakukan langsung kepada penjual melalui metode yang disepakati, dan penjual akan mengonfirmasi setelah memeriksa dana diterima.",
            ],
            'lokasi' => [
                'title' => 'Lokasi Desa Linonggasai',
                'content' => 'Desa Linonggasai berada di Kecamatan Wonggeduku Barat, Kabupaten Konawe, Sulawesi Tenggara. Produk dapat diambil langsung di lokasi penjual atau diantar sesuai kesepakatan.',
            ],
            'kontak' => [
                'title' => 'Kontak Pengelola',
                'content' => 'Untuk pertanyaan seputar L-DCH, pendaftaran penjual, atau kerja sama, silakan hubungi pengelola melalui kontak di bawah ini. Untuk pemesanan produk, hubungi penjual langsung melalui tombol WhatsApp pada halaman produk.',
            ],
        ];

        foreach ($pages as $slug => $page) {
            Page::query()->firstOrCreate(['slug' => $slug], $page);
        }
    }
}
