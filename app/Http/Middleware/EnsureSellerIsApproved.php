<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only verified sellers may manage their store, products and orders (blueprint 3).
 */
class EnsureSellerIsApproved
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isApprovedSeller()) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'Fitur ini hanya tersedia untuk penjual yang telah diverifikasi admin.');
        }

        return $next($request);
    }
}
