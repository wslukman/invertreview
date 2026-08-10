<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-user {--role=super_admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user for development';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $role = $this->option('role');

        $user = User::create([
            'name' => 'Test ' . ucfirst($role),
            'email' => strtolower($role) . '@unitedchurch.local',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $user->assignRole($role);

        $this->info("✓ Test user created successfully!");
        $this->line("  Email: {$user->email}");
        $this->line("  Password: password");
        $this->line("  Role: {$role}");
    }
}
