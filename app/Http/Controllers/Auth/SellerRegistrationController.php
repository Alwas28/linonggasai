<?php

namespace App\Http\Controllers\Auth;

use App\Enums\StoreStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SellerRegistrationRequest;
use App\Models\Setting;
use App\Models\Store;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Residents register as sellers; the account starts as pending until an admin verifies it (blueprint 4.1).
 */
class SellerRegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register-seller', [
            'registrationOpen' => Setting::get('accept_seller_registration') === '1',
        ]);
    }

    public function store(SellerRegistrationRequest $request): RedirectResponse
    {
        abort_unless(Setting::get('accept_seller_registration') === '1', 403, 'Pendaftaran penjual sedang ditutup.');

        $data = $request->validated();
        $phone = WhatsApp::normalize($data['phone']);

        $user = DB::transaction(function () use ($data, $phone): User {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $phone,
                'password' => $data['password'],
            ]);
            $user->role = UserRole::Seller;
            $user->status = UserStatus::Active;
            $user->save();

            $user->sellerProfile()->create([
                'address' => $data['address'],
                'village' => $data['village'],
                'phone' => $phone,
            ]);

            $store = new Store([
                'name' => $data['store_name'],
                'whatsapp_number' => $phone,
                'address' => $data['address'].', '.$data['village'],
            ]);
            $store->status = StoreStatus::Active;
            $user->store()->save($store);

            return $user;
        });

        ActivityLogger::log('seller.registered', $user->sellerProfile, user: $user);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('seller.dashboard')
            ->with('success', 'Pendaftaran berhasil. Akun Anda sedang menunggu verifikasi admin L-DCH.');
    }
}
