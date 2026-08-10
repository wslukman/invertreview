<?php

namespace Database\Seeders;

use App\Models\ProgramRegistration;
use App\Models\SocialProgram;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProgramRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = SocialProgram::where('status', '!=', 'cancelled')->get();
        $totalRegistrationsCreated = 0;

        foreach ($programs as $program) {
            // Ambil jumlah registrasi (30-60% dari capacity)
            $registrationCount = rand(
                (int) ($program->capacity * 0.3),
                (int) ($program->capacity * 0.6)
            );

            // Buat registrasi
            for ($i = 0; $i < $registrationCount; $i++) {
                // 60% dari user terdaftar, 40% dari tamu
                if (rand(1, 100) <= 60) {
                    $user = User::inRandomOrder()->first();

                    // Check apakah sudah terdaftar
                    $existingReg = ProgramRegistration::where('program_id', $program->id)
                        ->where('user_id', $user->id)
                        ->exists();

                    if ($existingReg) {
                        continue;
                    }

                    // 40% chance untuk attended
                    if (rand(1, 100) <= 40) {
                        ProgramRegistration::factory()->fromUser()->attended()->create([
                            'program_id' => $program->id,
                            'user_id' => $user->id,
                        ]);
                    } else {
                        ProgramRegistration::factory()->fromUser()->create([
                            'program_id' => $program->id,
                            'user_id' => $user->id,
                        ]);
                    }
                } else {
                    // Tamu - 20% chance untuk cancelled
                    if (rand(1, 100) <= 20) {
                        ProgramRegistration::factory()->fromGuest()->create([
                            'program_id' => $program->id,
                            'status' => 'cancelled',
                        ]);
                    } else {
                        ProgramRegistration::factory()->fromGuest()->create([
                            'program_id' => $program->id,
                        ]);
                    }
                }

                $totalRegistrationsCreated++;
            }

            // Update registered_count di program
            $program->registered_count = ProgramRegistration::where('program_id', $program->id)
                ->where('status', '!=', 'cancelled')
                ->count();
            $program->save();

            $this->command->line('  ✓ Created ' . $registrationCount . ' registrations for: ' . $program->title);
        }

        $this->command->info('✓ Program Registration seeding completed: ' . $totalRegistrationsCreated . ' registrations created');
    }
}
