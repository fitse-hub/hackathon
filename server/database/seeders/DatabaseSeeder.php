<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Phase 1 — Only two roles exist: manager & sales_officer.
     */
    public function run(): void
    {
        // ── Manager ─────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'manager@fitse.com'],
            [
                'name'     => 'Abebe Kebede',
                'password' => Hash::make('password123'),
                'role'     => 'manager',
            ]
        );

        // ── Sales Officer ───────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'sales@fitse.com'],
            [
                'name'     => 'Chaltu Tadesse',
                'password' => Hash::make('password123'),
                'role'     => 'sales_officer',
            ]
        );

        $this->command->info('');
        $this->command->info('✅  Phase 1 users seeded successfully!');
        $this->command->info('─────────────────────────────────────');
        $this->command->info('  Manager:        manager@fitse.com / password123');
        $this->command->info('  Sales Officer:  sales@fitse.com   / password123');
        $this->command->info('─────────────────────────────────────');
    }
}
