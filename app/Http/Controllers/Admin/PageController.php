<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::query()->whereIn('slug', Page::SLUGS)->get()->sortBy(fn (Page $page) => array_search($page->slug, Page::SLUGS)),
        ]);
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.form', ['page' => $page]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'content' => ['required', 'string', 'max:10000'],
        ], [], ['title' => 'judul', 'content' => 'isi halaman']);

        $page->update($data);
        ActivityLogger::log('page.updated', $page, ['slug' => $page->slug]);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman '.$page->title.' diperbarui.');
    }
}
