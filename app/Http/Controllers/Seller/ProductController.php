<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ProductStatus;
use App\Enums\StockStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store;
        $status = ProductStatus::tryFrom($request->string('status')->toString());
        $search = trim($request->string('q')->toString());

        $products = $store->products()
            ->with('category')
            ->when($status, fn (Builder $query) => $query->where('status', $status))
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('seller.products.index', [
            'products' => $products,
            'activeStatus' => $status,
            'search' => $search,
            'statusCounts' => $store->products()->toBase()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Product::class);

        return view('seller.products.form', [
            'product' => new Product(['stock_status' => StockStatus::Available]),
            'categories' => Category::query()->active()->ordered()->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $store = $request->user()->store;

        $product = DB::transaction(function () use ($request, $store): Product {
            $product = new Product($request->safe()->except(['main_image', 'gallery', 'remove_gallery', 'action']));
            $product->store()->associate($store);
            $product->status = $request->wantsSubmission() ? ProductStatus::Pending : ProductStatus::Draft;
            $product->main_image = $request->file('main_image')->store('products', 'public');
            $product->save();

            $this->storeGallery($request, $product);

            return $product;
        });

        ActivityLogger::log('product.created', $product, ['status' => $product->status->value]);

        return redirect()->route('seller.products.index')->with('success', $request->wantsSubmission()
            ? 'Produk dikirim dan menunggu moderasi admin L-DCH.'
            : 'Produk disimpan sebagai draf.');
    }

    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);

        return view('seller.products.form', [
            'product' => $product->load('images'),
            'categories' => Category::query()
                ->where(fn (Builder $query) => $query->active()->orWhere('id', $product->category_id))
                ->ordered()
                ->get(),
        ]);
    }

    /**
     * Any content change re-enters moderation so a seller can never publish edits without review.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $wasPublished = $product->status === ProductStatus::Published;

        DB::transaction(function () use ($request, $product): void {
            $product->fill($request->safe()->except(['main_image', 'gallery', 'remove_gallery', 'action']));
            $product->status = $request->wantsSubmission() ? ProductStatus::Pending : ProductStatus::Draft;
            $product->moderation_note = null;
            $product->approved_by = null;
            $product->approved_at = null;

            if ($request->hasFile('main_image')) {
                $previous = $product->main_image;
                $product->main_image = $request->file('main_image')->store('products', 'public');

                if ($previous) {
                    Storage::disk('public')->delete($previous);
                }
            }

            $product->save();

            $this->removeGallery($request, $product);
            $this->storeGallery($request, $product);
        });

        ActivityLogger::log('product.updated', $product, ['status' => $product->status->value]);

        $message = $request->wantsSubmission()
            ? ($wasPublished ? 'Perubahan dikirim. Produk tampil kembali setelah disetujui admin.' : 'Produk dikirim dan menunggu moderasi admin.')
            : 'Perubahan disimpan sebagai draf.';

        return redirect()->route('seller.products.index')->with('success', $message);
    }

    /**
     * Stock is operational data, so updating it does not require re-moderation.
     */
    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);

        $data = $request->validate([
            'stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'stock_status' => ['required', Rule::enum(StockStatus::class)],
        ]);

        $product->forceFill($data)->save();

        return back()->with('success', 'Stok '.$product->name.' diperbarui.');
    }

    public function archive(Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);

        $product->forceFill(['status' => ProductStatus::Archived])->save();
        ActivityLogger::log('product.archived', $product);

        return back()->with('success', $product->name.' diarsipkan dan tidak lagi tampil di katalog.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        $product->delete();
        ActivityLogger::log('product.deleted', $product, ['name' => $product->name]);

        return redirect()->route('seller.products.index')->with('success', 'Produk dihapus.');
    }

    private function storeGallery(ProductRequest $request, Product $product): void
    {
        $nextOrder = (int) $product->images()->max('sort_order') + 1;

        foreach ($request->file('gallery', []) as $index => $file) {
            $product->images()->create([
                'image_path' => $file->store('products', 'public'),
                'alt_text' => $product->name,
                'sort_order' => $nextOrder + $index,
            ]);
        }
    }

    private function removeGallery(ProductRequest $request, Product $product): void
    {
        $ids = $request->validated('remove_gallery', []);

        if ($ids === []) {
            return;
        }

        $product->images()->whereKey($ids)->get()->each(function ($image): void {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        });
    }
}
