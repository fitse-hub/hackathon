<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create 
                            {--name= : The name of the user}
                            {--email= : The email of the user}
                            {--password= : The password of the user}
                            {--role= : The role of the user (manager or sales_officer)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('           QMT User Creation Tool');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        // Get user input
        $name = $this->option('name') ?: $this->ask('Enter user name');
        $email = $this->option('email') ?: $this->ask('Enter email address');
        $password = $this->option('password') ?: $this->secret('Enter password (min 6 characters)');
        
        // Role selection
        $role = $this->option('role');
        if (!$role) {
            $role = $this->choice(
                'Select user role',
                ['manager', 'sales_officer'],
                0
            );
        }

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:manager,sales_officer',
        ]);

        if ($validator->fails()) {
            $this->newLine();
            $this->error('❌ Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('   • ' . $error);
            }
            return Command::FAILURE;
        }

        // Confirm creation
        $this->newLine();
        $this->info('User Details:');
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $name],
                ['Email', $email],
                ['Role', ucfirst(str_replace('_', ' ', $role))],
            ]
        );

        if (!$this->confirm('Create this user?', true)) {
            $this->warn('User creation cancelled.');
            return Command::SUCCESS;
        }

        // Create user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role,
            ]);

            $this->newLine();
            $this->info('═══════════════════════════════════════════════════════');
            $this->info('✅ User created successfully!');
            $this->info('═══════════════════════════════════════════════════════');
            $this->newLine();
            $this->info('User ID: ' . $user->id);
            $this->info('Name: ' . $user->name);
            $this->info('Email: ' . $user->email);
            $this->info('Role: ' . ucfirst(str_replace('_', ' ', $user->role)));
            $this->newLine();
            $this->info('The user can now login at: ' . config('app.frontend_url', 'http://localhost:5173'));
            $this->newLine();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Failed to create user: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
