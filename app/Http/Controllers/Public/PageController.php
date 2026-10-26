<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Renders the editable information pages: Tentang, Cara Pesan, Lokasi and Kontak.
 */
class PageController extends Controller
{
    public function show(string $slug): View
    {
        abort_unless(in_array($slug, Page::SLUGS, true), 404);

        return view('public.pages.'.$slug, [
            'page' => Page::findBySlug($slug),
        ]);
    }
}
