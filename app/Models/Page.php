<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Editable public information pages (Tentang, Cara Pesan, Lokasi, Kontak).
 * Content is plain text and always rendered escaped.
 */
#[Fillable(['slug', 'title', 'content'])]
class Page extends Model
{
    public const SLUGS = ['tentang', 'cara-pesan', 'lokasi', 'kontak'];

    public static function findBySlug(string $slug): ?self
    {
        return static::query()->where('slug', $slug)->first();
    }

    /**
     * Split the content into paragraphs separated by blank lines.
     *
     * @return list<string>
     */
    public function paragraphs(): array
    {
        $parts = preg_split('/\R{2,}/', trim($this->content)) ?: [];

        return array_values(array_filter(array_map('trim', $parts)));
    }
}
