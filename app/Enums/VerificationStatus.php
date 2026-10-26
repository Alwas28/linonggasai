<?php

namespace App\Enums;

/**
 * Status verifikasi penjual (blueprint 4.1).
 */
enum VerificationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Approved => 'Terverifikasi',
            self::Rejected => 'Ditolak',
            self::Suspended => 'Dinonaktifkan',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-harvest-100 text-harvest-700',
            self::Approved => 'bg-forest-100 text-forest-700',
            self::Rejected => 'bg-clay-50 text-clay-600',
            self::Suspended => 'bg-forest-50 text-forest-500',
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
