@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')
    <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center">
        <label for="aksi" class="text-sm font-semibold text-forest-500">Filter aksi</label>
        <select id="aksi" name="aksi" class="form-input mt-0 sm:max-w-xs" onchange="this.form.submit()">
            <option value="">Semua aktivitas</option>
            @foreach (\App\Models\ActivityLog::ACTIONS as $value => $label)
                <option value="{{ $value }}" @selected($activeAction === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <noscript><button class="btn-dark">Filter</button></noscript>
    </form>

    <div class="card mt-5 px-5 py-2">
        <div class="divide-y divide-forest-100">
            @forelse ($logs as $log)
                @include('admin.partials.activity-row', ['log' => $log])
            @empty
                <p class="py-8 text-center text-sm text-forest-500">Belum ada aktivitas.</p>
            @endforelse
        </div>
    </div>
    {{ $logs->links() }}
@endsection
