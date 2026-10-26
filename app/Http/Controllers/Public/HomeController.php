<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::query()
            ->active()
            ->ordered()
            ->withCount(['products' => fn (Builder $query) => $query->publiclyVisible()])
            ->get();

        $products = Product::query()
            ->publiclyVisible()
            ->with(['category', 'store'])
            ->latest('approved_at')
            ->take(9)
            ->get();

        return view('public.home', [
            'categories' => $categories,
            'products' => $products,
            'banners' => Banner::query()->active()->take(3)->get(),
            'about' => Page::findBySlug('tentang'),
            'stats' => [
                'categories' => $categories->count(),
                'products' => $categories->sum('products_count'),
                'stores' => Store::query()->visible()->count(),
            ],
        ]);
    }
}
