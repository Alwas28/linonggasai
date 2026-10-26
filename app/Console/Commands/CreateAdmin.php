<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Controlled provisioning of the first admin account (blueprint 8.3).
 */
class CreateAdmin extends Command
{
    protected $signature = 'ldch:create-admin
                            {--name= : Nama admin}
                            {--email= : Email admin}
                            {--password= : Kata sandi (akan ditanyakan bila tidak diisi)}';

    protected $description = 'Membuat akun admin L-DCH Linonggasai';

    public function handle(): int
    {
        $data = [
            'name' => $this->option('name') ?? $this->ask('Nama admin'),
            'email' => $this->option('email') ?? $this->ask('Email admin'),
            'password' => $this->option('password') ?? $this->secret('Kata sandi (min. 8 karakter)'),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User($data);
        $user->role = UserRole::Admin;
        $user->status = UserStatus::Active;
        $user->email_verified_at = now();
        $user->save();

        $this->info("Akun admin {$user->email} berhasil dibuat.");

        return self::SUCCESS;
    }
}
