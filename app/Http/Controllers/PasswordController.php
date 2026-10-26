<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

/**
 * Password change shared by the seller and admin settings pages.
 */
class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [], [
            'current_password' => 'kata sandi saat ini',
            'password' => 'kata sandi baru',
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('success', 'Kata sandi berhasil diganti.');
    }
}
