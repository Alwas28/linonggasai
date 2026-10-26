<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        return view('admin.banners.index', [
            'banners' => Banner::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.banners.form', ['banner' => new Banner(['is_active' => true])]);
    }

    public function store(BannerRequest $request): RedirectResponse
    {
        $banner = new Banner($request->safe()->except('image'));
        $banner->is_active = $request->boolean('is_active');
        $banner->image = $request->file('image')->store('banners', 'public');
        $banner->save();

        ActivityLogger::log('banner.saved', $banner, ['title' => $banner->title]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner ditambahkan.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.form', ['banner' => $banner]);
    }

    public function update(BannerRequest $request, Banner $banner): RedirectResponse
    {
        $banner->fill($request->safe()->except('image'));
        $banner->is_active = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $banner->image = $request->file('image')->store('banners', 'public');
        }

        $banner->save();
        ActivityLogger::log('banner.saved', $banner, ['title' => $banner->title]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner diperbarui.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();
        ActivityLogger::log('banner.deleted', null, ['title' => $banner->title]);

        return back()->with('success', 'Banner dihapus.');
    }
}
