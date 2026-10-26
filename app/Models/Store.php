<?php

namespace App\Models;

use App\Enums\StoreStatus;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Services\WhatsApp;
use Database\Factories\StoreFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'description', 'whatsapp_number', 'address', 'business_hours'])]
class Store extends Model
{
    /** @use HasFactory<StoreFactory> */
    use HasFactory, SoftDeletes;

    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StoreStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Store $store): void {
            $store->slug ??= static::uniqueSlug($store->name);
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'toko';
        $slug = $base;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(4));
        }

        return $slug;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Stores that may appear publicly: open, owned by an active and approved seller.
     */
    #[Scope]
    protected function visible(Builder $query): void
    {
        $query->where('status', StoreStatus::Active)
            ->whereHas('user', function (Builder $user): void {
                $user->where('status', UserStatus::Active)
                    ->whereHas('sellerProfile', fn (Builder $profile) => $profile->where('verification_status', VerificationStatus::Approved));
            });
    }

    public function isOpen(): bool
    {
        return $this->status === StoreStatus::Active;
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }

    public function whatsappUrl(?string $message = null): string
    {
        return WhatsApp::link($this->whatsapp_number, $message);
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)) ?: [])
            ->filter()
            ->take(2)
            ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');
    }
}
