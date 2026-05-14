<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a manager user
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MANAGER,
        ]);

        // Create a sales officer user
        User::create([
            'name' => 'Sales Officer',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SALES_OFFICER,
        ]);

        // Create additional test users
        User::create([
            'name' => 'John Manager',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MANAGER,
        ]);

        User::create([
            'name' => 'Jane Sales',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SALES_OFFICER,
        ]);
    }
}
