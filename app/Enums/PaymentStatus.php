<?php

namespace App\Enums;

/**
 * Status pembayaran yang dicatat manual (blueprint 5.3).
 */
enum PaymentStatus: string
{
    case Unconfirmed = 'unconfirmed';
    case ReportedPaid = 'reported_paid';
    case ConfirmedPaid = 'confirmed_paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Unconfirmed => 'Belum Dikonfirmasi',
            self::ReportedPaid => 'Dilaporkan Dibayar',
            self::ConfirmedPaid => 'Lunas (Dikonfirmasi)',
            self::Failed => 'Gagal',
            self::Refunded => 'Dikembalikan',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Unconfirmed => 'bg-forest-50 text-forest-500',
            self::ReportedPaid => 'bg-harvest-100 text-harvest-700',
            self::ConfirmedPaid => 'bg-forest-100 text-forest-700',
            self::Failed => 'bg-clay-50 text-clay-600',
            self::Refunded => 'bg-sky-100 text-sky-800',
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
