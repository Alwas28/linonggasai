<?php

namespace App\Rules;

use App\Services\WhatsApp;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WhatsAppNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! WhatsApp::isValid($value)) {
            $fail('Kolom :attribute harus berupa nomor HP/WhatsApp Indonesia yang valid, contoh 081234567890.');
        }
    }
}
