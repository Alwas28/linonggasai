<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'icon', 'sort_order', 'status'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CategoryStatus::class,
            'sort_order' => 'integer',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', CategoryStatus::Active);
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Gradient used as a placeholder when a product has no photo,
     * matching the colour of each category in the reference design.
     */
    public function gradientClasses(): string
    {
        return match ($this->slug) {
            'perikanan' => 'from-sky-400 to-sky-700',
            'umkm' => 'from-clay-400 to-clay-600',
            default => 'from-forest-400 to-forest-600',
        };
    }

    public function tagClasses(): string
    {
        return match ($this->slug) {
            'perikanan' => 'bg-sky-800',
            'umkm' => 'bg-clay-600',
            default => 'bg-forest-700',
        };
    }
}
