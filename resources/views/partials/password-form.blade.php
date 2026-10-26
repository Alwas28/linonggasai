<form method="POST" action="{{ route('password.change') }}" class="card space-y-4 p-5">
    @csrf @method('PUT')
    <h2 class="font-display font-bold">Ganti Kata Sandi</h2>
    @foreach ([
        ['current_password', 'Kata Sandi Saat Ini', 'current-password'],
        ['password', 'Kata Sandi Baru', 'new-password'],
        ['password_confirmation', 'Ulangi Kata Sandi Baru', 'new-password'],
    ] as [$name, $label, $autocomplete])
        <div>
            <label for="{{ $name }}" class="form-label">{{ $label }}</label>
            <input id="{{ $name }}" name="{{ $name }}" type="password" required autocomplete="{{ $autocomplete }}" class="form-input">
            @error($name, 'password')
                <p class="mt-1 text-xs font-semibold text-clay-600">{{ $message }}</p>
            @enderror
        </div>
    @endforeach
    <button class="btn-dark">Simpan Kata Sandi</button>
</form>
