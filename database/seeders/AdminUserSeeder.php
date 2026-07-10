<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin account.
     *
     * Idempotent: uses updateOrCreate so running the seeder repeatedly won't
     * create duplicates and will reset the admin's password/verification.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin.asset2026@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('P@ssw0rd'),
                'email_verified_at' => now(),
                'is_admin' => true,
                'is_super_admin' => true,
                'status' => 'active',
            ],
        );
    }
}
