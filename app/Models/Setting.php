<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value website configuration managed from /admin/pengaturan.
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    private const CACHE_KEY = 'settings.all';

    /**
     * Default values used until an admin changes them.
     *
     * @var array<string, string>
     */
    public const DEFAULTS = [
        'site_name' => 'L-DCH Linonggasai',
        'site_tagline' => 'Digital Creative Hub',
        'hero_badge' => 'Karang Taruna Tunas Maju',
        'hero_title' => 'Belanja Hasil Bumi & UMKM Desa Linonggasai',
        'hero_description' => 'L-DCH memasarkan hasil pertanian, perikanan, dan usaha warga Desa Linonggasai langsung dari kebun, kolam, dan dapur produksi ke pelanggan digital.',
        'admin_whatsapp' => '',
        'contact_email' => '',
        'address' => 'Kantor Desa Linonggasai, Kec. Wonggeduku Barat, Kab. Konawe, Sulawesi Tenggara',
        'manager_name' => 'Karang Taruna Tunas Maju',
        'accept_seller_registration' => '1',
    ];

    /**
     * @return array<string, string>
     */
    public static function values(): array
    {
        $stored = Cache::rememberForever(self::CACHE_KEY, fn (): array => static::query()->pluck('value', 'key')->all());

        return array_merge(self::DEFAULTS, array_filter($stored, fn ($value) => $value !== null));
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::values()[$key] ?? $default;
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }
}
