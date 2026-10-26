<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Enums\StockStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\SellerProfile;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo data based on the products shown in the reference design. Local/staging only.
 * All demo accounts use the password "password".
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrNew(['email' => 'admin@gmail.com']);
        $admin->forceFill([
            'name' => 'Admin Karang Taruna',
            'password' => 'password',
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
            'email_verified_at' => now(),
        ])->save();

        $categories = Category::query()->pluck('id', 'slug');

        $sellers = [
            ['Pak Hasan', 'hasan', 'Beras & Pupuk Pak Hasan', 'Dusun II', VerificationStatus::Approved, [
                ['Beras Lokal', 'pertanian', 14000, 'kg', 120, 'Hasil panen sawah warga, digiling segar sesuai pesanan.'],
                ['Beras Merah', 'pertanian', 18000, 'kg', 30, 'Beras merah lokal, kaya serat, cocok untuk menu sehat keluarga.'],
                ['Bibit Tanaman', 'pertanian', 3000, 'polybag', 200, 'Bibit unggul siap tanam untuk kebutuhan tani warga.'],
                ['Pupuk', 'pertanian', 110000, 'sak', 6, 'Pupuk untuk mendukung hasil panen yang lebih maksimal.'],
            ]],
            ['Ibu Wa Ode', 'waode', 'Sayur Kebun Wa Ode', 'Dusun I', VerificationStatus::Approved, [
                ['Sayur-Sayuran', 'pertanian', 5000, 'ikat', 40, 'Sayuran segar hasil kebun warga, dipanen sesuai musim.'],
            ]],
            ['Pak Rahman', 'rahman', 'Kolam Lele & Nila Rahman', 'Dusun III', VerificationStatus::Approved, [
                ['Ikan Lele', 'perikanan', 28000, 'kg', 35, 'Hasil budidaya kolam warga, segar dan siap olah.'],
                ['Ikan Nila', 'perikanan', 35000, 'kg', null, 'Dibudidayakan langsung di kolam desa, kualitas terjaga.'],
                ['Ikan Gurami', 'perikanan', 45000, 'kg', 12, 'Ukuran konsumsi, cocok untuk kebutuhan rumah makan.'],
            ]],
            ['Ibu Sitti', 'sitti', 'Kerupuk Rumahan Bu Sitti', 'Dusun II', VerificationStatus::Approved, [
                ['Kerupuk', 'umkm', 10000, 'bungkus', 60, 'Camilan renyah produksi rumahan warga desa.'],
            ]],
            ['Ibu Nurlia', 'nurlia', 'Kios Sembako Nurlia', 'Dusun IV', VerificationStatus::Approved, [
                ['Gas LPG 3 kg', 'umkm', 22000, 'tabung', 18, 'Pangkalan gas LPG untuk kebutuhan rumah tangga.'],
            ]],
            ['Andi Saputra', 'andi', 'Madu Hutan Andi', 'Dusun I', VerificationStatus::Pending, []],
            ['Ibu Hasmi', 'hasmi', 'Kue Tradisional Hasmi', 'Dusun III', VerificationStatus::Pending, []],
        ];

        foreach ($sellers as [$name, $handle, $storeName, $village, $verification, $products]) {
            $phone = '62812'.str_pad((string) crc32($handle), 8, '0', STR_PAD_LEFT);
            $phone = substr($phone, 0, 13);

            $user = User::query()->firstOrNew(['email' => $handle.'@linonggasai.test']);
            $user->forceFill([
                'name' => $name,
                'phone' => $phone,
                'password' => 'password',
                'role' => UserRole::Seller,
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ])->save();

            $profile = SellerProfile::query()->firstOrNew(['user_id' => $user->id]);
            $profile->forceFill([
                'address' => 'Jl. Poros Linonggasai',
                'village' => $village,
                'phone' => $phone,
                'verification_status' => $verification,
                'verified_by' => $verification === VerificationStatus::Approved ? $admin->id : null,
                'verified_at' => $verification === VerificationStatus::Approved ? now() : null,
            ])->save();

            $store = Store::query()->firstOrNew(['user_id' => $user->id]);
            $store->forceFill([
                'name' => $storeName,
                'slug' => $store->slug ?? Store::uniqueSlug($storeName),
                'description' => 'Usaha warga '.$village.', Desa Linonggasai.',
                'whatsapp_number' => $phone,
                'address' => $village.', Desa Linonggasai',
                'business_hours' => 'Setiap hari, 07.00 – 17.00 WITA',
                'status' => 'active',
            ])->save();

            foreach ($products as [$productName, $category, $price, $unit, $stock, $description]) {
                $product = $store->products()->firstOrNew(['name' => $productName]);
                $product->forceFill([
                    'category_id' => $categories[$category],
                    'description' => $description,
                    'price' => $price,
                    'unit' => $unit,
                    'stock' => $stock,
                    'stock_status' => $stock === null ? StockStatus::OutOfStock : StockStatus::Available,
                    'status' => ProductStatus::Published,
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                ])->save();
            }
        }

        $hasan = Store::query()->whereHas('user', fn ($query) => $query->where('email', 'hasan@linonggasai.test'))->first();

        if ($hasan && $hasan->orders()->doesntExist()) {
            $beras = $hasan->products()->where('name', 'Beras Lokal')->first();

            $order = new Order(['buyer_name' => 'Rina (Unaaha)', 'buyer_phone' => '6281200000001', 'notes' => 'Antar ke rumah', 'order_status' => OrderStatus::New->value, 'payment_status' => PaymentStatus::Unconfirmed->value]);
            $order->store()->associate($hasan);
            $order->total_amount = 140000;
            $order->save();
            $order->items()->create(['product_id' => $beras->id, 'product_name' => $beras->name, 'unit_price' => 14000, 'quantity' => 10, 'subtotal' => 140000]);
        }

        $pupuk = $hasan?->products()->where('name', 'Pupuk')->first();
        if ($pupuk) {
            $pupuk->forceFill(['status' => ProductStatus::Pending, 'approved_at' => null, 'approved_by' => null])->save();
        }

        $this->call(DemoOrderSeeder::class);
    }
}
