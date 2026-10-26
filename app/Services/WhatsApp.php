<?php

namespace App\Services;

use App\Models\Product;

/**
 * Builds wa.me links. The website never handles payment; it only opens a chat
 * between buyer and seller (blueprint 4.3 & 4.4).
 */
class WhatsApp
{
    /**
     * Normalize an Indonesian phone number to the international format used by wa.me (62xxxxxxxxx).
     */
    public static function normalize(?string $number): string
    {
        $digits = preg_replace('/\D+/', '', (string) $number) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    public static function isValid(?string $number): bool
    {
        return (bool) preg_match('/^628\d{7,12}$/', static::normalize($number));
    }

    public static function link(string $number, ?string $message = null): string
    {
        $url = 'https://wa.me/'.static::normalize($number);

        return $message === null || $message === '' ? $url : $url.'?text='.rawurlencode($message);
    }

    public static function orderMessage(Product $product, int|string $quantity = '[Jumlah]'): string
    {
        return implode("\n", [
            'Halo, saya tertarik memesan produk melalui L-DCH Linonggasai.',
            '',
            'Nama produk: '.$product->name,
            'Toko: '.$product->store->name,
            'Harga yang tercantum: '.$product->formattedPrice().' / '.$product->unit,
            'Jumlah yang ingin dipesan: '.$quantity,
            '',
            'Mohon informasi ketersediaan produk, total pembayaran, metode pembayaran, serta pilihan pengambilan atau pengiriman. Terima kasih.',
        ]);
    }

    /**
     * Format a stored number for display, e.g. 6281234567890 -> 0812-3456-7890.
     */
    public static function display(?string $number): string
    {
        $digits = static::normalize($number);

        if ($digits === '') {
            return '-';
        }

        $local = str_starts_with($digits, '62') ? '0'.substr($digits, 2) : $digits;

        return trim(implode('-', str_split($local, 4)), '-');
    }
}
