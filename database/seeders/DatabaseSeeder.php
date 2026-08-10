<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Church;
use App\Models\SocialProgram;
use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- 1. SETUP ROLE & PERMISSIONS (SPATIE) ---
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Church Management
            'view_all_churches', 'approve_church', 'reject_church', 'suspend_church',
            
            // Program Management
            'manage_programs', 'create_program', 'edit_program', 'delete_program',
            
            // Activity Management
            'manage_activities', 'view_activities', 'create_activity', 'edit_activity', 'delete_activity',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $churchAdminRole = Role::firstOrCreate(['name' => 'church_admin', 'guard_name' => 'web']);
        $memberRole = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);

        $superAdminRole->syncPermissions(Permission::all());
        
        $churchAdminRole->syncPermissions([
            'manage_programs', 
            'manage_activities', 
            'create_activity', 
            'edit_activity'
        ]);

        // --- 2. BUAT USER ADMIN UTAMA ---
        $admin = User::updateOrCreate(
            ['email' => 'admin@united.com'],
            [
                'name' => 'Super Admin United',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole($superAdminRole);
        }

        // --- 3. BUAT DATA GEREJA (CHURCH) ---
        $churchApproved = Church::updateOrCreate(
            ['name' => 'Gereja United Pusat'],
            [
                'address' => 'Jl. Jend. Sudirman No. 1, Jakarta Pusat',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'phone' => '0215551234',
                'email' => 'pusat@church.com',
                'description' => 'Pusat koordinasi pemetaan global United dan ekosistem qq.',
                'founded_year' => 2020,
                'status' => 'approved',
                'is_active' => true,
                'submitted_by' => $admin->id,
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]
        );

        $churchBandung = Church::updateOrCreate(
            ['name' => 'Gereja United Bandung'],
            [
                'address' => 'Jl. Braga No. 10, Bandung',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'phone' => '022999888',
                'email' => 'bandung@church.com',
                'description' => 'Cabang baru United di wilayah Jawa Barat.',
                'founded_year' => 2024,
                'status' => 'pending',
                'is_active' => false,
                'submitted_by' => $admin->id,
            ]
        );

        // --- 4. BUAT PROGRAM SOSIAL ---
        SocialProgram::updateOrCreate(
            ['title' => 'Bantuan Sembako United'],
            [
                'church_id' => $churchApproved->id,
                'description' => 'Program pembagian sembako untuk masyarakat sekitar gereja.',
                'type' => 'pembagian_sembako',
                'status' => 'active',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(3)->toDateString(),
                'capacity' => 100,
                'registered_count' => 0,
                'contact_person' => 'Budi Staff',
                'contact_phone' => '08123456789',
            ]
        );

        // --- 5. BUAT DATA AKTIVITAS (ACTIVITIES) ---
        $activities = [
            [
                'church_id' => $churchApproved->id,
                'user_id' => $admin->id,
                'title' => 'Workshop Blockchain & Token $DUIT',
                'content' => 'Pelatihan mendalam mengenai utilitas token $DUIT dalam ekosistem qq untuk efisiensi transaksi komunitas.',
                'type' => 'kegiatan_sosial',
                'activity_date' => now()->addDays(2),
                'is_published' => true,
            ],
            [
                'church_id' => $churchApproved->id,
                'user_id' => $admin->id,
                'title' => 'Ibadah Raya United Global',
                'content' => 'Ibadah perdana tahun 2026 sekaligus peluncuran fitur mapping global pada platform United.',
                'type' => 'ibadah',
                'activity_date' => now()->addDays(5),
                'is_published' => true,
            ],
            [
                'church_id' => $churchBandung->id,
                'user_id' => $admin->id,
                'title' => 'Sosialisasi Node United Bandung',
                'content' => 'Pertemuan strategis mengenai penempatan node infrastruktur World Smart Chain di wilayah Jawa Barat.',
                'type' => 'kegiatan_sosial',
                'activity_date' => now()->addDays(10),
                'is_published' => true,
            ],
        ];

        foreach ($activities as $actData) {
            Activity::updateOrCreate(
                ['title' => $actData['title']],
                $actData
            );
        }

        $this->command->info('✅ Database United & qq Ecosystem berhasil diperbarui!');
        $this->command->info('👤 Admin: admin@united.com | Pass: password123');
    }
}