<?php

namespace App\Http\Requests\Seller;

use App\Rules\WhatsAppNumber;
use App\Services\WhatsApp;
use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'whatsapp_number' => ['required', 'string', 'max:20', new WhatsAppNumber],
            'address' => ['nullable', 'string', 'max:255'],
            'business_hours' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'current_password' => $this->changesWhatsApp()
                ? ['required', 'current_password']
                : ['nullable'],
        ];
    }

    /**
     * Changing the WhatsApp number redirects every buyer, so it requires the
     * account password to protect against account takeover (blueprint 8.3).
     */
    public function changesWhatsApp(): bool
    {
        $store = $this->user()->store;

        return $store !== null
            && WhatsApp::normalize($this->input('whatsapp_number')) !== WhatsApp::normalize($store->whatsapp_number);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama toko',
            'description' => 'deskripsi',
            'whatsapp_number' => 'nomor WhatsApp',
            'address' => 'alamat',
            'business_hours' => 'jam operasional',
            'current_password' => 'kata sandi saat ini',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Masukkan kata sandi Anda untuk mengganti nomor WhatsApp toko.',
            'current_password.current_password' => 'Kata sandi yang dimasukkan salah.',
        ];
    }
}
