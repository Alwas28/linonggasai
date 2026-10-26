<?php

namespace App\Http\Requests\Seller;

use App\Enums\CategoryStatus;
use App\Enums\StockStatus;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public const MAX_PRICE = 100_000_000;

    public const MAX_GALLERY_IMAGES = 4;

    public function authorize(): bool
    {
        $product = $this->route('product');

        Gate::authorize(...($product instanceof Product ? ['update', $product] : ['create', Product::class]));

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $existingGallery = $product instanceof Product ? $product->images()->count() : 0;

        return [
            ...self::contentRules($product instanceof Product ? $product : null),
            'gallery' => ['nullable', 'array', 'max:'.max(0, self::MAX_GALLERY_IMAGES - $existingGallery)],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['integer'],
            'action' => ['required', Rule::in(['draft', 'submit'])],
        ];
    }

    /**
     * Product content rules shared by the seller form and the admin edit form.
     *
     * @return array<string, mixed>
     */
    public static function contentRules(?Product $product = null): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('status', CategoryStatus::Active->value),
            ],
            'description' => ['required', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:'.self::MAX_PRICE, 'decimal:0,2'],
            'unit' => ['required', 'string', 'max:30'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'stock_status' => ['required', Rule::enum(StockStatus::class)],
            'main_image' => [
                $product?->main_image ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:min_width=200,min_height=200,max_width=6000,max_height=6000',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function contentAttributes(): array
    {
        return (new self)->attributes();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama produk',
            'category_id' => 'kategori',
            'description' => 'deskripsi',
            'price' => 'harga',
            'unit' => 'satuan',
            'stock' => 'stok',
            'stock_status' => 'status ketersediaan',
            'main_image' => 'foto utama',
            'gallery' => 'foto tambahan',
            'gallery.*' => 'foto tambahan',
        ];
    }

    public function wantsSubmission(): bool
    {
        return $this->input('action') === 'submit';
    }
}
