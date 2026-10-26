<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::query()->ordered()->withCount('products')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::query()->create($request->validated());
        ActivityLogger::log('category.saved', $category, ['name' => $category->name]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori '.$category->name.' ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());
        ActivityLogger::log('category.saved', $category, ['name' => $category->name]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori '.$category->name.' diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->withTrashed()->exists()) {
            return back()->with('error', 'Kategori masih dipakai produk. Nonaktifkan saja agar tidak tampil.');
        }

        $category->delete();
        ActivityLogger::log('category.deleted', null, ['name' => $category->name]);

        return back()->with('success', 'Kategori '.$category->name.' dihapus.');
    }
}
