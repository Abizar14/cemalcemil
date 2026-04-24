<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Booth',
                'email' => 'admin@booth.test',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Kasir Booth',
                'email' => 'kasir@booth.test',
                'role' => 'kasir',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => $user['password'],
                ],
            );
        }
    }
}
