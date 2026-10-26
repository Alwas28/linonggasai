@extends('layouts.admin')

@section('title', 'Detail Penjual')

@use('App\Enums\VerificationStatus')

@section('content')
    @php($profile = $seller->sellerProfile)

    <a href="{{ route('admin.sellers.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Daftar Penjual</a>

    <div class="mt-3 grid gap-4 lg:grid-cols-3">
        <div class="space-y-4">
            <div class="card p-5 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-harvest-500 font-display text-xl font-extrabold text-white">{{ $seller->initials() }}</div>
                <h2 class="mt-2 font-display text-lg font-extrabold">{{ $seller->name }}</h2>
                <p class="text-sm text-forest-500">{{ $seller->email }}</p>
                <div class="mt-3 flex flex-wrap justify-center gap-2">
                    @if ($profile)
                        <x-badge :status="$profile->verification_status" />
                    @endif
                    <x-badge :status="$seller->status" />
                </div>
            </div>

            <div class="card p-5 text-sm">
                <h3 class="font-display font-bold">Data Penjual</h3>
                <dl class="mt-3 space-y-2">
                    <div><dt class="text-xs text-forest-400">HP/WhatsApp</dt><dd class="font-semibold">{{ \App\Services\WhatsApp::display($profile?->phone) }}</dd></div>
                    <div><dt class="text-xs text-forest-400">Alamat</dt><dd class="font-semibold">{{ $profile?->address }}, {{ $profile?->village }}</dd></div>
                    <div><dt class="text-xs text-forest-400">Terdaftar</dt><dd class="font-semibold">{{ $seller->created_at->translatedFormat('d F Y') }}</dd></div>
                    <div><dt class="text-xs text-forest-400">Terakhir masuk</dt><dd class="font-semibold">{{ $seller->last_login_at?->diffForHumans() ?? '-' }}</dd></div>
                    @if ($profile?->verified_at)
                        <div><dt class="text-xs text-forest-400">Diputuskan oleh</dt><dd class="font-semibold">{{ $profile->verifier?->name ?? '-' }} · {{ $profile->verified_at->translatedFormat('d M Y') }}</dd></div>
                    @endif
                    @if ($profile?->verification_note)
                        <div><dt class="text-xs text-forest-400">Catatan</dt><dd class="font-semibold">{{ $profile->verification_note }}</dd></div>
                    @endif
                </dl>
            </div>

            <div class="card space-y-2 p-5">
                <h3 class="font-display font-bold">Tindakan</h3>
                @if ($profile?->verification_status === VerificationStatus::Pending)
                    <a href="{{ route('admin.verification.index') }}" class="btn-dark w-full">Periksa Pendaftaran</a>
                @elseif ($profile?->verification_status === VerificationStatus::Approved)
                    @include('admin.partials.reject-form', [
                        'action' => route('admin.sellers.suspend', $seller),
                        'id' => 'suspend',
                        'label' => 'Nonaktifkan Penjual',
                        'submit' => 'Nonaktifkan',
                        'placeholder' => 'Alasan penonaktifan (mis. pelanggaran ketentuan)…',
                        'class' => 'block',
                    ])
                @elseif ($profile?->verification_status === VerificationStatus::Suspended)
                    <form method="POST" action="{{ route('admin.sellers.reactivate', $seller) }}">
                        @csrf @method('PATCH')
                        <button class="btn-dark w-full">Aktifkan Kembali</button>
                    </form>
                @else
                    <p class="text-sm text-forest-500">Menunggu penjual memperbaiki data dan mengajukan ulang.</p>
                @endif
                @error('note')
                    <p class="text-xs font-semibold text-clay-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="space-y-4 lg:col-span-2">
            @if ($seller->store)
                <div class="card p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="font-display font-bold">{{ $seller->store->name }}</h3>
                            <p class="text-xs text-forest-500">WhatsApp toko: {{ \App\Services\WhatsApp::display($seller->store->whatsapp_number) }} · {{ $seller->store->business_hours ?? 'Jam operasional belum diisi' }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-badge :status="$seller->store->status" />
                            <a href="{{ route('admin.stores.edit', $seller) }}" class="text-xs font-bold text-harvest-600">Edit</a>
                        </div>
                    </div>
                    @if ($seller->store->description)
                        <p class="mt-3 text-sm text-forest-600">{{ $seller->store->description }}</p>
                    @endif
                </div>
            @endif

            <div class="card overflow-hidden">
                <div class="px-5 pb-2 pt-4 font-display font-bold">Produk ({{ $products->count() }})</div>
                <div class="divide-y divide-forest-100">
                    @forelse ($products as $product)
                        <a href="{{ route('admin.products.show', $product) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-cream">
                            <x-product-thumb :product="$product" class="h-10 w-10 shrink-0 rounded-lg" icon-size="text-lg" />
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-bold">{{ $product->name }}</div>
                                <div class="text-xs text-forest-500">{{ $product->formattedPrice() }} / {{ $product->unit }} · {{ $product->category->name }}</div>
                            </div>
                            <x-badge :status="$product->status" />
                        </a>
                    @empty
                        <p class="px-5 py-6 text-sm text-forest-500">Belum ada produk.</p>
                    @endforelse
                </div>
            </div>

            <div class="card px-5 py-2">
                <div class="pb-1 pt-2 font-display font-bold">Aktivitas Penjual</div>
                <div class="divide-y divide-forest-100">
                    @forelse ($activities as $log)
                        @include('admin.partials.activity-row', ['log' => $log->setRelation('user', $seller)])
                    @empty
                        <p class="py-4 text-sm text-forest-500">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
