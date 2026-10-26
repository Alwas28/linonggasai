<?php

namespace App\Enums;

/**
 * Status publikasi produk (blueprint 4.2).
 */
enum ProductStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Published = 'published';
    case Rejected = 'rejected';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Pending => 'Menunggu Moderasi',
            self::Published => 'Tayang',
            self::Rejected => 'Ditolak',
            self::Archived => 'Diarsipkan',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-forest-50 text-forest-500',
            self::Pending => 'bg-harvest-100 text-harvest-700',
            self::Published => 'bg-forest-100 text-forest-700',
            self::Rejected => 'bg-clay-50 text-clay-600',
            self::Archived => 'bg-forest-50 text-forest-500',
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
