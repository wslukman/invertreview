<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add wslukman@gmail.com as a user if they don't exist
        $user = User::firstOrCreate(
            ['email' => 'wslukman@gmail.com'],
            [
                'name' => 'Wslukman Admin',
                'password' => Hash::make(Str::random(12)), // They can reset password via forgot password if needed
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Ensure super_admin role exists
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        
        // Assign role if not already assigned
        if (!$user->hasRole('super_admin')) {
            $user->assignRole($superAdminRole);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $user = User::where('email', 'wslukman@gmail.com')->first();
        if ($user && $user->hasRole('super_admin')) {
            $user->removeRole('super_admin');
        }
    }
};
