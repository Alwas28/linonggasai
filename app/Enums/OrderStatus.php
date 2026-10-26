<?php

namespace App\Enums;

/**
 * Tahapan pemenuhan rekap pesanan (blueprint 5.3).
 */
enum OrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Ready = 'ready';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Baru',
            self::Confirmed => 'Dikonfirmasi',
            self::Processing => 'Diproses',
            self::Ready => 'Siap Diserahkan',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::New => 'bg-harvest-100 text-harvest-700',
            self::Confirmed => 'bg-sky-100 text-sky-800',
            self::Processing => 'bg-sky-100 text-sky-800',
            self::Ready => 'bg-forest-100 text-forest-700',
            self::Completed => 'bg-forest-700 text-cream',
            self::Cancelled => 'bg-clay-50 text-clay-600',
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
