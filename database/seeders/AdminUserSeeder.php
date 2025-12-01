<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@example.com';
        $password = Str::password(16, true, true, true, false);

        if (User::where('email', $email)->exists()) {
            $this->command->info('Admin user already exists.');
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->command->info('Admin user created successfully.');
        $this->command->info("Email: {$email}");
        $this->command->info("Password: {$password}");
    }
}
