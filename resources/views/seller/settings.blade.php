@extends('layouts.seller')

@section('title', 'Pengaturan')

@section('content')
    <a href="{{ route('seller.account') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Akun</a>
    <h1 class="mt-2 font-display text-2xl font-extrabold">Pengaturan Akun</h1>

    <div class="mt-5 grid gap-4 md:grid-cols-2">
        @include('partials.password-form')

        <div class="card space-y-3 p-5 text-sm">
            <h2 class="font-display font-bold">Informasi Akun</h2>
            <div><span class="text-forest-400">Email</span><div class="font-semibold">{{ $user->email }}</div></div>
            <div><span class="text-forest-400">Terakhir masuk</span><div class="font-semibold">{{ $user->last_login_at?->translatedFormat('d F Y, H:i') ?? '-' }}</div></div>
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button class="btn-danger">Keluar</button>
            </form>
        </div>
    </div>

    <div class="card mt-4 px-5 py-2">
        <div class="pb-1 pt-2">
            <h2 class="font-display font-bold">Riwayat Aktivitas</h2>
            <p class="text-xs text-forest-500">Aktivitas Anda serta keputusan pengelola terkait akun, toko, dan produk Anda.</p>
        </div>
        <div class="divide-y divide-forest-100">
            @forelse ($logs as $log)
                <div class="flex items-start gap-3 py-3 text-sm">
                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full {{ $log->user_id === $user->id ? 'bg-forest-50' : 'bg-harvest-100' }} text-xs" aria-hidden="true">{{ $log->user_id === $user->id ? '👤' : '🛡️' }}</div>
                    <div class="min-w-0 flex-1">
                        <div>
                            <span class="font-bold">{{ $log->user_id === $user->id ? 'Anda' : 'Admin L-DCH' }}</span>
                            <span class="text-forest-600">{{ mb_strtolower($log->description()) }}</span>
                        </div>
                        @php($details = collect($log->metadata ?? [])->only(['name', 'code', 'note'])->filter())
                        @if ($details->isNotEmpty())
                            <div class="truncate text-xs text-forest-500">{{ $details->implode(' · ') }}</div>
                        @endif
                    </div>
                    <time class="shrink-0 text-[11px] text-forest-400" datetime="{{ $log->created_at?->toIso8601String() }}">{{ $log->created_at?->diffForHumans(short: true) }}</time>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-forest-500">Belum ada aktivitas.</p>
            @endforelse
        </div>
    </div>
    {{ $logs->links() }}
@endsection
