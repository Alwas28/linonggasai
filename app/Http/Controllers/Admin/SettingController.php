<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Rules\WhatsAppNumber;
use App\Services\ActivityLogger;
use App\Services\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', [
            'values' => Setting::values(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['nullable', 'string', 'max:100'],
            'hero_badge' => ['nullable', 'string', 'max:100'],
            'hero_title' => ['required', 'string', 'max:150'],
            'hero_description' => ['required', 'string', 'max:500'],
            'admin_whatsapp' => ['nullable', 'string', 'max:20', new WhatsAppNumber],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'manager_name' => ['required', 'string', 'max:100'],
        ], [], [
            'site_name' => 'nama website',
            'hero_title' => 'judul hero',
            'hero_description' => 'deskripsi hero',
            'admin_whatsapp' => 'WhatsApp pengelola',
            'address' => 'alamat',
            'manager_name' => 'nama pengelola',
        ]);

        $data['admin_whatsapp'] = $data['admin_whatsapp'] ? WhatsApp::normalize($data['admin_whatsapp']) : '';
        $data['accept_seller_registration'] = $request->boolean('accept_seller_registration') ? '1' : '0';

        Setting::put($data);
        ActivityLogger::log('settings.updated');

        return back()->with('success', 'Pengaturan website disimpan.');
    }
}
