<?php

namespace App\Enums;

/**
 * Status login akun pengguna.
 */
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Inactive => 'Nonaktif',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Active => 'bg-forest-100 text-forest-700',
            self::Inactive => 'bg-clay-50 text-clay-600',
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
