<?php

namespace App\Http\Controllers\Public;

use App\Enums\CategoryStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Allowed sort options mapped to [column, direction].
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const SORTS = [
        'terbaru' => ['approved_at', 'desc'],
        'termurah' => ['price', 'asc'],
        'termahal' => ['price', 'desc'],
        'nama' => ['name', 'asc'],
    ];

    public function index(Request $request): View
    {
        $category = $request->filled('kategori')
            ? Category::query()->active()->where('slug', $request->string('kategori'))->first()
            : null;

        return $this->catalog($request, $category);
    }

    public function category(Request $request, Category $category): View
    {
        abort_unless($category->status === CategoryStatus::Active, 404);

        return $this->catalog($request, $category);
    }

    public function show(Product $product): View
    {
        abort_unless($product->isPubliclyVisible(), 404);

        $product->load(['category', 'store', 'images']);

        $related = Product::query()
            ->publiclyVisible()
            ->with(['category', 'store'])
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->latest('approved_at')
            ->take(3)
            ->get();

        return view('public.products.show', [
            'product' => $product,
            'related' => $related,
        ]);
    }

    private function catalog(Request $request, ?Category $category): View
    {
        $sort = self::SORTS[$request->string('urut')->toString()] ?? self::SORTS['terbaru'];
        $search = trim($request->string('q')->toString());

        $products = Product::query()
            ->publiclyVisible()
            ->with(['category', 'store'])
            ->when($category, fn (Builder $query) => $query->where('category_id', $category->id))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->orderBy(...$sort)
            ->paginate(12)
            ->withQueryString();

        return view('public.products.index', [
            'products' => $products,
            'categories' => Category::query()->active()->ordered()->get(),
            'activeCategory' => $category,
            'search' => $search,
            'sort' => array_key_exists($request->string('urut')->toString(), self::SORTS) ? $request->string('urut')->toString() : 'terbaru',
        ]);
    }
}
