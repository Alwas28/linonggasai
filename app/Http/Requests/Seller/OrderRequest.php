<?php

namespace App\Http\Requests\Seller;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isApprovedSeller();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'buyer_name' => ['required', 'string', 'max:100'],
            'buyer_phone' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'order_status' => ['required', Rule::enum(OrderStatus::class)],
            'payment_status' => ['required', Rule::enum(PaymentStatus::class)],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('store_id', $this->user()->store->id)->whereNull('deleted_at'),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:'.ProductRequest::MAX_PRICE],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'buyer_name' => 'nama pembeli',
            'buyer_phone' => 'nomor pembeli',
            'order_status' => 'status pesanan',
            'payment_status' => 'status pembayaran',
            'items' => 'produk',
            'items.*.product_id' => 'produk',
            'items.*.quantity' => 'jumlah',
            'items.*.unit_price' => 'harga satuan',
        ];
    }
}
