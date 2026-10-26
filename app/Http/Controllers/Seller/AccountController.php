<?php

namespace App\Http\Controllers\Seller;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\Store;
use App\Rules\WhatsAppNumber;
use App\Services\ActivityLogger;
use App\Services\WhatsApp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Seller account data. A rejected seller may correct the data and resubmit it for verification.
 */
class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        return view('seller.account', [
            'user' => $request->user()->load(['sellerProfile', 'store']),
        ]);
    }

    /**
     * Settings page with the seller's limited activity log: their own actions and
     * admin decisions about their account, store and products (blueprint 3: "Terbatas").
     */
    public function settings(Request $request): View
    {
        $user = $request->user()->load(['sellerProfile', 'store']);
        $productIds = $user->store?->products()->withTrashed()->pluck('id') ?? collect();

        $logs = ActivityLog::query()
            ->with('user')
            ->where(function (Builder $query) use ($user, $productIds): void {
                $query->where('user_id', $user->id)
                    ->orWhere(fn (Builder $query) => $query->where('subject_type', (new SellerProfile)->getMorphClass())->where('subject_id', $user->sellerProfile?->id))
                    ->orWhere(fn (Builder $query) => $query->where('subject_type', (new Store)->getMorphClass())->where('subject_id', $user->store?->id))
                    ->orWhere(fn (Builder $query) => $query->where('subject_type', (new Product)->getMorphClass())->whereIn('subject_id', $productIds));
            })
            ->latest('created_at')
            ->latest('id')
            ->paginate(15);

        return view('seller.settings', [
            'user' => $user,
            'logs' => $logs,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->sellerProfile;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20', new WhatsAppNumber],
            'address' => ['required', 'string', 'max:255'],
            'village' => ['required', 'string', 'max:100'],
        ]);

        $phone = WhatsApp::normalize($data['phone']);

        $user->fill(['name' => $data['name'], 'email' => $data['email'], 'phone' => $phone])->save();
        $profile->fill(['address' => $data['address'], 'village' => $data['village'], 'phone' => $phone]);

        $resubmitted = $profile->verification_status === VerificationStatus::Rejected;

        if ($resubmitted) {
            $profile->verification_status = VerificationStatus::Pending;
        }

        $profile->save();

        if ($resubmitted) {
            ActivityLogger::log('seller.resubmitted', $profile);
        }

        return back()->with('success', $resubmitted
            ? 'Data diperbarui dan diajukan ulang untuk verifikasi admin.'
            : 'Data akun berhasil diperbarui.');
    }
}
