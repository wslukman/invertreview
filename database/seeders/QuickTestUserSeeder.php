<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class QuickTestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create multiple test accounts for quick testing
        $testAccounts = [
            [
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Church Admin',
                'email' => 'church@test.com',
                'role' => 'church_admin',
            ],
            [
                'name' => 'Member Test',
                'email' => 'member@test.com',
                'role' => 'member',
            ],
        ];

        foreach ($testAccounts as $account) {
            $user = User::create([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => bcrypt('password'),
                'is_active' => true,
            ]);

            $user->assignRole($account['role']);
            $this->command->line("✓ Created {$account['role']}: {$account['email']}");
        }

        $this->command->info('Quick test users created successfully!');
    }
}
