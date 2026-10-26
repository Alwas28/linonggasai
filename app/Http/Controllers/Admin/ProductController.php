<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Admin corrections to a seller's product (e.g. typo, wrong category or price).
 * Publication status is unchanged; it is managed through moderation.
 */
class ProductController extends Controller
{
    public function edit(Product $product): View
    {
        Gate::authorize('moderate', $product);

        return view('admin.products.form', [
            'product' => $product->load('store'),
            'categories' => Category::query()
                ->where(fn (Builder $query) => $query->active()->orWhere('id', $product->category_id))
                ->ordered()
                ->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('moderate', $product);

        $data = $request->validate(ProductRequest::contentRules($product), [], ProductRequest::contentAttributes());

        $product->fill(collect($data)->except('main_image')->all());

        if ($request->hasFile('main_image')) {
            $previous = $product->main_image;
            $product->main_image = $request->file('main_image')->store('products', 'public');

            if ($previous) {
                Storage::disk('public')->delete($previous);
            }
        }

        $changed = array_keys($product->getDirty());
        $product->save();

        ActivityLogger::log('product.updated', $product, ['name' => $product->name, 'by_admin' => true, 'fields' => $changed]);

        return redirect()->route('admin.products.show', $product)->with('success', 'Produk '.$product->name.' diperbarui.');
    }
}
