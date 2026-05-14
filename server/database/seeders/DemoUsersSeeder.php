<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoUsers = [
            [
                'name' => 'Admin Demo',
                'email' => 'admin@demo.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Manager Demo',
                'email' => 'manager@demo.com',
                'password' => Hash::make('password123'),
                'role' => 'manager',
            ],
            [
                'name' => 'Moderator Demo',
                'email' => 'moderator@demo.com',
                'password' => Hash::make('password123'),
                'role' => 'moderator',
            ],
            [
                'name' => 'User Demo',
                'email' => 'user@demo.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
        ];

        foreach ($demoUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Demo users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@demo.com / password123');
        $this->command->info('Manager: manager@demo.com / password123');
        $this->command->info('Moderator: moderator@demo.com / password123');
        $this->command->info('User: user@demo.com / password123');
    }
}