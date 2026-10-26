@extends('layouts.admin')

@section('title', 'Periksa Produk')

@use('App\Enums\ProductStatus')
@use('App\Enums\VerificationStatus')

@section('content')
    <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Antrean Moderasi</a>

    <div class="mt-3 grid gap-5 lg:grid-cols-5">
        <div class="space-y-3 lg:col-span-2">
            <x-product-thumb :product="$product" class="aspect-square rounded-3xl" icon-size="text-8xl" />
            @if (! $product->main_image)
                <p class="rounded-xl bg-clay-50 px-3 py-2 text-xs font-semibold text-clay-600">Produk ini belum memiliki foto utama.</p>
            @endif
            @if ($product->images->isNotEmpty())
                <div class="grid grid-cols-4 gap-2">
                    @foreach ($product->images as $image)
                        <a href="{{ $image->url() }}" target="_blank" rel="noopener"><img src="{{ $image->url() }}" alt="" class="aspect-square w-full rounded-lg object-cover"></a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-4 lg:col-span-3">
            <div class="card p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <span class="tag rounded-l-sm rounded-r-md px-3 py-1 text-[11px] font-bold uppercase tracking-wide {{ $product->category->tagClasses() }}">{{ $product->category->name }}</span>
                        <h2 class="mt-3 font-display text-2xl font-extrabold">{{ $product->name }}</h2>
                    </div>
                    <x-badge :status="$product->status" />
                </div>
                <div class="mt-2 font-display text-xl font-extrabold text-harvest-600">{{ $product->formattedPrice() }} <span class="text-sm font-semibold text-forest-500">/ {{ $product->unit }}</span></div>
                <div class="mt-1 text-sm text-forest-500">{{ $product->stockLabel() }}</div>
                <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-forest-600">{{ $product->description }}</div>
                @if ($product->moderation_note)
                    <p class="mt-4 rounded-xl bg-forest-50 px-3 py-2 text-xs"><strong>Catatan moderasi:</strong> {{ $product->moderation_note }}</p>
                @endif
                @if ($product->approved_at)
                    <p class="mt-2 text-xs text-forest-400">Disetujui {{ $product->approver?->name }} · {{ $product->approved_at->translatedFormat('d F Y H:i') }}</p>
                @endif
            </div>

            <div class="card p-5 text-sm">
                <h3 class="font-display font-bold">Penjual</h3>
                <div class="mt-2 flex items-center justify-between gap-3">
                    <div>
                        <a href="{{ route('admin.sellers.show', $product->store->user) }}" class="font-bold hover:text-harvest-600">{{ $product->store->name }}</a>
                        <div class="text-xs text-forest-500">{{ $product->store->user->name }} · WA {{ \App\Services\WhatsApp::display($product->store->whatsapp_number) }}</div>
                    </div>
                    @if ($product->store->user->sellerProfile)
                        <x-badge :status="$product->store->user->sellerProfile->verification_status" />
                    @endif
                </div>
                @if ($product->store->user->sellerProfile?->verification_status !== VerificationStatus::Approved)
                    <p class="mt-2 text-xs font-semibold text-clay-600">Penjual belum/tidak lagi terverifikasi — produk tidak akan tampil meskipun disetujui.</p>
                @endif
            </div>

            <div class="card space-y-3 p-5">
                <h3 class="font-display font-bold">Keputusan Moderasi</h3>
                <p class="text-xs text-forest-500">Periksa foto, kategori, deskripsi, harga, dan kelayakan produk.</p>
                @error('note')
                    <p class="text-xs font-semibold text-clay-600">{{ $message }}</p>
                @enderror
                <div class="flex flex-wrap items-start gap-2">
                    @can('approve', $product)
                        <form method="POST" action="{{ route('admin.products.approve', $product) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <button class="w-full rounded-full bg-forest-700 py-2 text-xs font-bold text-cream">✓ Setujui & Tayangkan</button>
                        </form>
                    @endcan
                    @if (in_array($product->status, [ProductStatus::Pending, ProductStatus::Published], true))
                        @include('admin.partials.reject-form', ['action' => route('admin.products.reject', $product), 'id' => 'product'])
                    @endif
                    @if ($product->status !== ProductStatus::Archived)
                        @include('admin.partials.reject-form', [
                            'action' => route('admin.products.archive', $product),
                            'id' => 'archive',
                            'label' => 'Arsipkan',
                            'submit' => 'Arsipkan Produk',
                            'placeholder' => 'Alasan pengarsipan…',
                        ])
                    @endif
                </div>
                <div class="flex flex-wrap gap-4 text-xs font-bold">
                    @can('moderate', $product)
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-forest-600 hover:text-forest-700">✎ Koreksi data produk</a>
                    @endcan
                    @if ($product->status === ProductStatus::Published)
                        <a href="{{ route('products.show', $product) }}" target="_blank" class="text-harvest-600">Lihat di katalog ↗</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
