<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Admin review of seller registrations (blueprint 4.1).
 */
class SellerVerificationController extends Controller
{
    public function index(): View
    {
        return view('admin.seller-verification', [
            'pending' => SellerProfile::query()
                ->with('user.store')
                ->where('verification_status', VerificationStatus::Pending)
                ->oldest('updated_at')
                ->get(),
            'recentlyRejected' => SellerProfile::query()
                ->with(['user.store', 'verifier'])
                ->where('verification_status', VerificationStatus::Rejected)
                ->latest('verified_at')
                ->take(10)
                ->get(),
        ]);
    }

    public function approve(Request $request, SellerProfile $sellerProfile): RedirectResponse
    {
        Gate::authorize('verify', $sellerProfile);
        abort_unless($sellerProfile->verification_status === VerificationStatus::Pending, 422, 'Pendaftaran ini sudah diproses.');

        $sellerProfile->markAs(VerificationStatus::Approved, $request->user());
        ActivityLogger::log('seller.approved', $sellerProfile, ['seller' => $sellerProfile->user->name]);

        return back()->with('success', $sellerProfile->user->name.' disetujui sebagai penjual.');
    }

    public function reject(Request $request, SellerProfile $sellerProfile): RedirectResponse
    {
        Gate::authorize('verify', $sellerProfile);
        abort_unless($sellerProfile->verification_status === VerificationStatus::Pending, 422, 'Pendaftaran ini sudah diproses.');

        $data = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ], [], ['note' => 'alasan penolakan']);

        $sellerProfile->markAs(VerificationStatus::Rejected, $request->user(), $data['note']);
        ActivityLogger::log('seller.rejected', $sellerProfile, ['seller' => $sellerProfile->user->name, 'note' => $data['note']]);

        return back()->with('success', 'Pendaftaran '.$sellerProfile->user->name.' ditolak.');
    }
}
