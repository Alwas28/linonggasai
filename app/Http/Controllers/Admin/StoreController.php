<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StoreStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\WhatsAppNumber;
use App\Services\ActivityLogger;
use App\Services\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Admin correction of a seller's store profile when needed (blueprint 3: "Ya, bila perlu").
 */
class StoreController extends Controller
{
    public function edit(User $seller): View
    {
        abort_unless($seller->isSeller() && $seller->store, 404);

        return view('admin.stores.form', [
            'seller' => $seller,
            'store' => $seller->store,
        ]);
    }

    public function update(Request $request, User $seller): RedirectResponse
    {
        abort_unless($seller->isSeller() && $seller->store, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'whatsapp_number' => ['required', 'string', 'max:20', new WhatsAppNumber],
            'address' => ['nullable', 'string', 'max:255'],
            'business_hours' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::enum(StoreStatus::class)],
        ], [], [
            'name' => 'nama toko',
            'whatsapp_number' => 'nomor WhatsApp',
            'business_hours' => 'jam operasional',
        ]);

        $store = $seller->store;
        $store->fill($data);
        $store->whatsapp_number = WhatsApp::normalize($data['whatsapp_number']);
        $store->status = StoreStatus::from($data['status']);

        $changed = array_keys($store->getDirty());
        $store->save();

        ActivityLogger::log('store.updated', $store, ['name' => $store->name, 'by_admin' => true, 'fields' => $changed]);

        return redirect()->route('admin.sellers.show', $seller)->with('success', 'Profil toko '.$store->name.' diperbarui.');
    }
}
