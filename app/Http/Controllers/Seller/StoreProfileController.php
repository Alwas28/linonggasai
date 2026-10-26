<?php

namespace App\Http\Controllers\Seller;

use App\Enums\StoreStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreProfileRequest;
use App\Services\ActivityLogger;
use App\Services\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoreProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('seller.store-profile', [
            'store' => $request->user()->store,
        ]);
    }

    public function update(StoreProfileRequest $request): RedirectResponse
    {
        $store = $request->user()->store;
        $changedWhatsApp = $request->changesWhatsApp();

        $store->fill($request->safe()->except(['logo', 'current_password']));
        $store->whatsapp_number = WhatsApp::normalize($request->validated('whatsapp_number'));

        if ($request->hasFile('logo')) {
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }

            $store->logo = $request->file('logo')->store('stores', 'public');
        }

        $store->save();

        ActivityLogger::log('store.updated', $store, $changedWhatsApp ? ['whatsapp_changed' => true] : []);

        return back()->with('success', 'Profil toko berhasil diperbarui.');
    }

    public function toggle(Request $request): RedirectResponse
    {
        $store = $request->user()->store;
        $store->status = $store->isOpen() ? StoreStatus::Inactive : StoreStatus::Active;
        $store->save();

        ActivityLogger::log('store.updated', $store, ['status' => $store->status->value]);

        return back()->with('success', $store->isOpen()
            ? 'Toko dibuka. Produk yang disetujui kembali tampil di katalog.'
            : 'Toko ditutup sementara. Produk tidak tampil di katalog.');
    }
}
