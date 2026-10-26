<?php

namespace App\Enums;

/**
 * Peran akun pengguna.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Seller = 'seller';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Seller => 'Penjual',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Admin => 'bg-forest-700 text-cream',
            self::Seller => 'bg-forest-100 text-forest-700',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
