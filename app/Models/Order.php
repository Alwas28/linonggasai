<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

/**
 * Optional administrative record of an order agreed on WhatsApp.
 * The website never processes money (blueprint 4.3).
 */
#[Fillable(['buyer_name', 'buyer_phone', 'notes', 'order_status', 'payment_status'])]
class Order extends Model
{
    protected $attributes = [
        'order_status' => 'new',
        'payment_status' => 'unconfirmed',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order_status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            $order->order_code ??= static::generateCode();
        });
    }

    public static function generateCode(): string
    {
        do {
            $code = 'LD-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (static::query()->where('order_code', $code)->exists());

        return $code;
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function formattedTotal(): string
    {
        return 'Rp '.Number::format((float) $this->total_amount, locale: 'id');
    }
}
