<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat super admin
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@unitedchurch.local',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');
        $this->command->line('  ✓ Created Super Admin: superadmin@unitedchurch.local');

        // Ambil gereja yang sudah approved
        $approvedChurches = Church::where('status', 'approved')->get();

        foreach ($approvedChurches as $church) {
            // Buat church admin untuk setiap gereja
            $churchAdmin = User::factory()->create([
                'name' => 'Admin ' . $church->name,
                'email' => 'admin-' . strtolower(str_replace(' ', '-', substr($church->name, 0, 10))) . '@unitedchurch.local',
                'phone' => '08' . $this->faker->numerify('#########'),
                'church_id' => $church->id,
                'is_active' => true,
                'password' => bcrypt('password'),
            ]);
            $churchAdmin->assignRole('church_admin');
            $this->command->line('  ✓ Created Church Admin for: ' . $church->name);

            // Buat 5 member untuk setiap gereja
            $members = User::factory(5)->create([
                'church_id' => $church->id,
                'is_active' => true,
                'password' => bcrypt('password'),
            ]);

            foreach ($members as $member) {
                $member->assignRole('member');
            }
            $this->command->line('  ✓ Created 5 members for: ' . $church->name);
        }

        // Buat beberapa guest users tanpa church (untuk testing)
        $guestUser = User::factory()->create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'password' => bcrypt('password'),
            'church_id' => null,
            'is_active' => true,
        ]);
        $guestUser->assignRole('member');
        $this->command->line('  ✓ Created Guest user for testing');

        $this->command->info('✓ Test User seeding completed: ' . User::count() . ' users created');
    }
}
