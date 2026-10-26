@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
    <div class="flex items-center justify-between gap-3">
        <p class="text-sm text-forest-500">Kategori nonaktif tidak tampil di katalog dan tidak bisa dipilih penjual.</p>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary shrink-0">+ Kategori</a>
    </div>

    <div class="card mt-5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-forest-50 text-left text-xs uppercase tracking-wide text-forest-500">
                    <tr><th class="px-5 py-3">Kategori</th><th class="px-5 py-3">Slug</th><th class="px-5 py-3 text-right">Produk</th><th class="px-5 py-3">Urutan</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-forest-100">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-cream">
                            <td class="px-5 py-3"><div class="font-bold">{{ $category->icon }} {{ $category->name }}</div><div class="max-w-xs truncate text-xs text-forest-500">{{ $category->description }}</div></td>
                            <td class="px-5 py-3 text-forest-500">{{ $category->slug }}</td>
                            <td class="px-5 py-3 text-right font-bold">{{ $category->products_count }}</td>
                            <td class="px-5 py-3">{{ $category->sort_order }}</td>
                            <td class="px-5 py-3"><x-badge :status="$category->status" /></td>
                            <td class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-harvest-600 hover:text-harvest-700">Edit</a>
                                @if ($category->products_count === 0)
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="ml-3 inline" data-confirm="Hapus kategori {{ $category->name }}?">
                                        @csrf @method('DELETE')
                                        <button class="text-clay-600">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-forest-500">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
