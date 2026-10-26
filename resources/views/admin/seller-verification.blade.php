@extends('layouts.admin')

@section('title', 'Verifikasi Penjual')

@section('content')
    <div class="rounded-2xl border border-harvest-100 bg-harvest-50 p-5">
        <h2 class="font-display font-bold">Menunggu Verifikasi ({{ $pending->count() }})</h2>
        <p class="mt-0.5 text-xs text-forest-500">Periksa data warga yang mendaftar sebagai penjual. Penolakan wajib disertai alasan.</p>

        @error('note')
            <p class="mt-2 text-xs font-semibold text-clay-600">{{ $message }}</p>
        @enderror

        <div class="mt-4 grid gap-3 md:grid-cols-2">
            @forelse ($pending as $profile)
                <div class="rounded-2xl bg-white p-4">
                    <div class="flex items-center gap-3">
                        <div class="grid h-11 w-11 place-items-center rounded-full bg-harvest-100 font-display font-bold text-harvest-700">{{ $profile->user->initials() }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate font-bold">{{ $profile->user->name }}</div>
                            <div class="text-xs text-forest-500">{{ $profile->user->store?->name }} · {{ $profile->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1.5 rounded-xl bg-forest-50 p-3 text-xs">
                        <dt class="text-forest-400">Email</dt><dd class="truncate font-semibold">{{ $profile->user->email }}</dd>
                        <dt class="text-forest-400">HP/WhatsApp</dt>
                        <dd class="font-semibold"><a href="{{ \App\Services\WhatsApp::link($profile->phone) }}" target="_blank" rel="noopener" class="text-harvest-600">{{ \App\Services\WhatsApp::display($profile->phone) }}</a></dd>
                        <dt class="text-forest-400">Dusun</dt><dd class="font-semibold">{{ $profile->village }}</dd>
                        <dt class="text-forest-400">Alamat</dt><dd class="font-semibold">{{ $profile->address }}</dd>
                    </dl>
                    @if ($profile->verification_note)
                        <p class="mt-2 text-xs text-forest-500"><strong>Catatan sebelumnya:</strong> {{ $profile->verification_note }}</p>
                    @endif
                    <div class="mt-3 flex items-start gap-2">
                        @include('admin.partials.reject-form', ['action' => route('admin.verification.reject', $profile), 'id' => 'seller-'.$profile->id])
                        <form method="POST" action="{{ route('admin.verification.approve', $profile) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <button class="w-full rounded-full bg-forest-700 py-2 text-xs font-bold text-cream">Setujui</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-forest-500 md:col-span-2">Semua pendaftar sudah diverifikasi ✓</p>
            @endforelse
        </div>
    </div>

    <h2 class="mb-3 mt-8 font-display font-bold">Penolakan Terakhir</h2>
    @if ($recentlyRejected->isEmpty())
        <p class="text-sm text-forest-500">Belum ada pendaftaran yang ditolak.</p>
    @else
        <div class="card divide-y divide-forest-100">
            @foreach ($recentlyRejected as $profile)
                <div class="flex flex-wrap items-center gap-3 px-5 py-3 text-sm">
                    <a href="{{ route('admin.sellers.show', $profile->user) }}" class="font-bold hover:text-harvest-600">{{ $profile->user->name }}</a>
                    <span class="text-forest-500">{{ $profile->verification_note }}</span>
                    <span class="ml-auto text-xs text-forest-400">oleh {{ $profile->verifier?->name ?? '-' }} · {{ $profile->verified_at?->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    @endif
@endsection
