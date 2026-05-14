<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Seed demo users for Phase 1.
     *
     * Roles: manager, sales_officer — nothing else.
     */
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Abebe Kebede',
                'email'    => 'manager@fitse.com',
                'password' => Hash::make('password123'),
                'role'     => 'manager',
            ],
            [
                'name'     => 'Chaltu Tadesse',
                'email'    => 'sales@fitse.com',
                'password' => Hash::make('password123'),
                'role'     => 'sales_officer',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        $this->command->info('✅  Demo users (manager + sales_officer) created.');
        $this->command->info('  Manager:        manager@fitse.com / password123');
        $this->command->info('  Sales Officer:  sales@fitse.com   / password123');
    }
}