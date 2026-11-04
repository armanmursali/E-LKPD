<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResetUser2PasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newPassword = 'password123';

        $user = User::find(2);

        if (! $user) {
            $this->command?->error('User dengan ID 2 tidak ditemukan di database.');

            return;
        }

        $user->forceFill([
            'password' => Hash::make($newPassword),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        $this->command?->info("Password untuk User ID 2 ({$user->email}) berhasil diubah menjadi: {$newPassword}");
    }
}

