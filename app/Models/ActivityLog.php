<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['user_id', 'action', 'subject_type', 'subject_id', 'metadata'])]
class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    /**
     * Human readable descriptions for logged actions.
     *
     * @var array<string, string>
     */
    public const ACTIONS = [
        'auth.login' => 'Masuk ke sistem',
        'seller.registered' => 'Mendaftar sebagai penjual',
        'seller.resubmitted' => 'Mengajukan ulang pendaftaran',
        'seller.approved' => 'Menyetujui penjual',
        'seller.rejected' => 'Menolak penjual',
        'seller.suspended' => 'Menonaktifkan penjual',
        'seller.reactivated' => 'Mengaktifkan kembali penjual',
        'store.updated' => 'Memperbarui profil toko',
        'product.created' => 'Menambah produk',
        'product.updated' => 'Mengubah produk',
        'product.submitted' => 'Mengajukan produk untuk moderasi',
        'product.archived' => 'Mengarsipkan produk',
        'product.deleted' => 'Menghapus produk',
        'product.approved' => 'Menyetujui produk',
        'product.rejected' => 'Menolak produk',
        'order.created' => 'Mencatat pesanan',
        'order.updated' => 'Memperbarui status pesanan',
        'category.saved' => 'Menyimpan kategori',
        'category.deleted' => 'Menghapus kategori',
        'banner.saved' => 'Menyimpan banner',
        'banner.deleted' => 'Menghapus banner',
        'page.updated' => 'Memperbarui halaman informasi',
        'user.created' => 'Membuat akun admin',
        'user.status_changed' => 'Mengubah status akun',
        'settings.updated' => 'Memperbarui pengaturan website',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function description(): string
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }
}
