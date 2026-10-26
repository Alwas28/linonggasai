<?php

namespace App\Enums;

/**
 * Status ketersediaan manual bila stok tidak dilacak.
 */
enum StockStatus: string
{
    case Available = 'available';
    case PreOrder = 'pre_order';
    case OutOfStock = 'out_of_stock';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::PreOrder => 'Pre-order',
            self::OutOfStock => 'Habis',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Available => 'bg-forest-100 text-forest-700',
            self::PreOrder => 'bg-sky-100 text-sky-800',
            self::OutOfStock => 'bg-clay-50 text-clay-600',
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
