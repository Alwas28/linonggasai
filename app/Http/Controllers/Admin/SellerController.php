<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function index(Request $request): View
    {
        $status = VerificationStatus::tryFrom($request->string('status')->toString());
        $search = trim($request->string('q')->toString());

        $sellers = User::query()
            ->where('role', UserRole::Seller)
            ->with([
                'sellerProfile',
                'store' => fn ($query) => $query->withCount(['products as published_products_count' => fn (Builder $products) => $products->where('status', ProductStatus::Published)]),
            ])
            ->when($status, fn (Builder $query) => $query->whereHas('sellerProfile', fn (Builder $profile) => $profile->where('verification_status', $status)))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhereHas('store', fn (Builder $store) => $store->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.sellers.index', [
            'sellers' => $sellers,
            'activeStatus' => $status,
            'search' => $search,
        ]);
    }

    public function show(User $seller): View
    {
        abort_unless($seller->isSeller(), 404);

        $seller->load(['sellerProfile.verifier', 'store']);

        return view('admin.sellers.show', [
            'seller' => $seller,
            'products' => $seller->store?->products()->with('category')->latest('updated_at')->get() ?? collect(),
            'activities' => $seller->activityLogs()->latest('created_at')->take(10)->get(),
        ]);
    }

    public function suspend(Request $request, User $seller): RedirectResponse
    {
        abort_unless($seller->isSeller(), 404);
        Gate::authorize('verify', $seller->sellerProfile);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ], [], ['note' => 'alasan penonaktifan']);

        $seller->sellerProfile->markAs(VerificationStatus::Suspended, $request->user(), $data['note']);
        ActivityLogger::log('seller.suspended', $seller->sellerProfile, ['seller' => $seller->name, 'note' => $data['note']]);

        return back()->with('success', $seller->name.' dinonaktifkan. Produknya tidak tampil di katalog.');
    }

    public function reactivate(Request $request, User $seller): RedirectResponse
    {
        abort_unless($seller->isSeller(), 404);
        Gate::authorize('verify', $seller->sellerProfile);
        abort_unless($seller->sellerProfile->verification_status === VerificationStatus::Suspended, 422);

        $seller->sellerProfile->markAs(VerificationStatus::Approved, $request->user());
        ActivityLogger::log('seller.reactivated', $seller->sellerProfile, ['seller' => $seller->name]);

        return back()->with('success', $seller->name.' diaktifkan kembali.');
    }
}
