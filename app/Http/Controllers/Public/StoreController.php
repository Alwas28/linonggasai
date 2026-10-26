<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function show(Store $store): View
    {
        abort_unless(Store::query()->visible()->whereKey($store->getKey())->exists(), 404);

        $products = $store->products()
            ->publiclyVisible()
            ->with(['category', 'store'])
            ->latest('approved_at')
            ->paginate(12);

        return view('public.stores.show', [
            'store' => $store->load('user.sellerProfile'),
            'products' => $products,
        ]);
    }
}
