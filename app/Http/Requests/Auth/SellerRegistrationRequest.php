<?php

namespace App\Http\Requests\Auth;

use App\Rules\WhatsAppNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SellerRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', new WhatsAppNumber],
            'password' => ['required', 'confirmed', Password::defaults()],
            'store_name' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'village' => ['required', 'string', 'max:100'],
            'terms' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'phone' => 'nomor HP/WhatsApp',
            'password' => 'kata sandi',
            'store_name' => 'nama usaha/toko',
            'address' => 'alamat',
            'village' => 'dusun',
            'terms' => 'persetujuan syarat penggunaan',
        ];
    }
}
