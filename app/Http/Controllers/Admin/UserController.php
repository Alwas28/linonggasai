<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Accounts and roles. Admin accounts are provisioned here only, never via public registration.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = UserRole::tryFrom($request->string('peran')->toString());
        $search = trim($request->string('q')->toString());

        return view('admin.users.index', [
            'users' => User::query()
                ->when($role, fn (Builder $query) => $query->where('role', $role))
                ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')))
                ->orderBy('role')
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
            'activeRole' => $role,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [], ['name' => 'nama', 'phone' => 'nomor HP', 'password' => 'kata sandi']);

        $user = new User($data);
        $user->role = UserRole::Admin;
        $user->status = UserStatus::Active;
        $user->email_verified_at = now();
        $user->save();

        ActivityLogger::log('user.created', $user, ['email' => $user->email]);

        return redirect()->route('admin.users.index')->with('success', 'Akun admin '.$user->name.' dibuat.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->status = $user->isActive() ? UserStatus::Inactive : UserStatus::Active;
        $user->save();

        ActivityLogger::log('user.status_changed', $user, ['status' => $user->status->value]);

        return back()->with('success', 'Akun '.$user->name.' sekarang '.mb_strtolower($user->status->label()).'.');
    }
}
