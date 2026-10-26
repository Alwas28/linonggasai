<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Admin moderation of products before they appear on the catalog (blueprint 4.2).
 */
class ProductModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->has('status')
            ? ProductStatus::tryFrom($request->string('status')->toString())
            : ProductStatus::Pending;
        $category = $request->integer('kategori') ?: null;
        $search = trim($request->string('q')->toString());

        $products = Product::query()
            ->with(['store', 'category'])
            ->when($status, fn (Builder $query) => $query->where('status', $status))
            ->when($category, fn (Builder $query) => $query->where('category_id', $category))
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
            ->when($status === ProductStatus::Pending, fn (Builder $query) => $query->oldest('updated_at'), fn (Builder $query) => $query->latest('updated_at'))
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'activeStatus' => $status,
            'activeCategory' => $category,
            'search' => $search,
            'categories' => Category::query()->ordered()->get(),
            'statusCounts' => Product::query()->toBase()->whereNull('deleted_at')->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Product $product): View
    {
        return view('admin.products.show', [
            'product' => $product->load(['store.user.sellerProfile', 'category', 'images', 'approver']),
        ]);
    }

    public function approve(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('approve', $product);

        $product->forceFill([
            'status' => ProductStatus::Published,
            'moderation_note' => null,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ])->save();

        ActivityLogger::log('product.approved', $product, ['name' => $product->name]);

        return $this->backToQueue($product, 'Produk '.$product->name.' disetujui dan tayang di katalog.');
    }

    public function reject(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('moderate', $product);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ], [], ['note' => 'alasan penolakan']);

        $product->forceFill([
            'status' => ProductStatus::Rejected,
            'moderation_note' => $data['note'],
            'approved_by' => $request->user()->id,
            'approved_at' => null,
        ])->save();

        ActivityLogger::log('product.rejected', $product, ['name' => $product->name, 'note' => $data['note']]);

        return $this->backToQueue($product, 'Produk '.$product->name.' ditolak.');
    }

    public function archive(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('moderate', $product);

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $product->forceFill([
            'status' => ProductStatus::Archived,
            'moderation_note' => $data['note'] ?? 'Diarsipkan oleh admin.',
        ])->save();

        ActivityLogger::log('product.archived', $product, ['name' => $product->name]);

        return back()->with('success', 'Produk '.$product->name.' diarsipkan.');
    }

    private function backToQueue(Product $product, string $message): RedirectResponse
    {
        $next = Product::query()
            ->where('status', ProductStatus::Pending)
            ->whereKeyNot($product->getKey())
            ->oldest('updated_at')
            ->first();

        return ($next ? redirect()->route('admin.products.show', $next) : redirect()->route('admin.products.index'))
            ->with('success', $message);
    }
}
