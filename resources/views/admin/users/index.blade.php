@extends('layouts.admin')

@section('title', 'Pengguna')

@use('App\Enums\UserRole')

@section('content')
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="chip {{ $activeRole ? '' : 'chip-active' }}">Semua</a>
            @foreach (UserRole::cases() as $role)
                <a href="{{ route('admin.users.index', ['peran' => $role->value]) }}" class="chip {{ $activeRole === $role ? 'chip-active' : '' }}">{{ $role->label() }}</a>
            @endforeach
        </div>
        <form method="GET" class="flex-1 sm:max-w-xs">
            @if ($activeRole)
                <input type="hidden" name="peran" value="{{ $activeRole->value }}">
            @endif
            <label for="q" class="sr-only">Cari pengguna</label>
            <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau email…" class="form-input mt-0">
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn-primary ml-auto">+ Akun Admin</a>
    </div>

    <div class="card mt-5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-forest-50 text-left text-xs uppercase tracking-wide text-forest-500">
                    <tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Peran</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Terakhir Masuk</th><th class="px-5 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-forest-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-cream">
                            <td class="px-5 py-3"><div class="font-bold">{{ $user->name }}</div><div class="text-xs text-forest-500">{{ $user->email }}</div></td>
                            <td class="px-5 py-3"><x-badge :status="$user->role" /></td>
                            <td class="px-5 py-3"><x-badge :status="$user->status" /></td>
                            <td class="px-5 py-3 text-xs text-forest-500">{{ $user->last_login_at?->diffForHumans() ?? 'Belum pernah' }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold">
                                @if ($user->isSeller())
                                    <a href="{{ route('admin.sellers.show', $user) }}" class="mr-3 text-forest-500 hover:text-forest-700">Detail</a>
                                @endif
                                @unless ($user->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.users.status', $user) }}" class="inline" data-confirm="{{ $user->isActive() ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $user->name }}?">
                                        @csrf @method('PATCH')
                                        <button class="{{ $user->isActive() ? 'text-clay-600' : 'text-forest-600' }}">{{ $user->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                    </form>
                                @else
                                    <span class="text-forest-300">Anda</span>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{ $users->links() }}
@endsection
