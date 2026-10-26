<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Enums\StockStatus;
use App\Services\WhatsApp;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

/**
 * Status, moderation and approval fields are never mass assignable so that a
 * seller can not publish their own product (blueprint 3 & 7).
 */
#[Fillable(['category_id', 'name', 'description', 'price', 'unit', 'stock', 'stock_status'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $attributes = [
        'status' => 'draft',
        'stock_status' => 'available',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'status' => ProductStatus::class,
            'stock_status' => StockStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            $product->slug ??= static::uniqueSlug($product->name);
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(4));
        }

        return $slug;
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Products shown on the public catalog: published, in an active category,
     * and sold by a visible store with an approved seller (blueprint 4.2).
     */
    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('status', ProductStatus::Published)
            ->whereHas('category', fn (Builder $category) => $category->where('status', CategoryStatus::Active))
            ->whereHas('store', fn (Builder $store) => $store->visible());
    }

    public function isPubliclyVisible(): bool
    {
        return static::query()->publiclyVisible()->whereKey($this->getKey())->exists();
    }

    public function mainImageUrl(): ?string
    {
        return $this->main_image ? Storage::disk('public')->url($this->main_image) : null;
    }

    public function formattedPrice(): string
    {
        return 'Rp '.Number::format((float) $this->price, locale: 'id');
    }

    public function stockLabel(): string
    {
        if ($this->stock_status !== StockStatus::Available) {
            return $this->stock_status->label();
        }

        if ($this->stock === null) {
            return 'Tersedia';
        }

        return $this->stock === 0 ? 'Stok habis' : 'Stok: '.$this->stock.' '.$this->unit;
    }

    public function whatsappMessage(int|string $quantity = '[Jumlah]'): string
    {
        return WhatsApp::orderMessage($this, $quantity);
    }

    public function whatsappUrl(int|string $quantity = '[Jumlah]'): string
    {
        return WhatsApp::link($this->store->whatsapp_number, $this->whatsappMessage($quantity));
    }
}
