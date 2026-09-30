<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->whereIn('email', ['admin@gmail.com', 'admin@example.com'])
            ->orderByRaw("email = 'admin@gmail.com' desc")
            ->first();

        if ($admin) {
            $admin->forceFill([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => 'Stuck@123',
            ])->save();
        } else {
            User::query()->create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => 'Stuck@123',
            ]);
        }
    }
}
