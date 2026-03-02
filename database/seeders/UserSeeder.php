<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@adip.store'],
            [
                'name'              => 'Administrator',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $admin->assignRole('admin');

        $demo = User::firstOrCreate(
            ['email' => 'demo@adip.store'],
            [
                'name'              => 'Demo User',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $demo->assignRole('user');
    }
}
