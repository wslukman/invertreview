<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\SocialProgram;
use App\Models\User;
use Illuminate\Database\Seeder;

class SocialProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $approvedChurches = Church::where('status', 'approved')->get();
        $totalProgramsCreated = 0;

        foreach ($approvedChurches as $church) {
            // Ambil church admin
            $churchAdmin = User::where('church_id', $church->id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'church_admin');
                })
                ->first();

            if (!$churchAdmin) {
                continue;
            }

            // Buat 2-4 program sosial untuk setiap gereja
            $programCount = rand(2, 4);

            for ($i = 0; $i < $programCount; $i++) {
                $status = array_rand(['active' => 1, 'draft' => 1, 'completed' => 1], 1);

                if ($status === 'active') {
                    SocialProgram::factory()->active()->create([
                        'church_id' => $church->id,
                        'created_by' => $churchAdmin->id,
                    ]);
                } elseif ($status === 'draft') {
                    SocialProgram::factory()->draft()->create([
                        'church_id' => $church->id,
                        'created_by' => $churchAdmin->id,
                    ]);
                } else {
                    SocialProgram::factory()->completed()->create([
                        'church_id' => $church->id,
                        'created_by' => $churchAdmin->id,
                    ]);
                }

                $totalProgramsCreated++;
            }

            $this->command->line('  ✓ Created ' . $programCount . ' social programs for: ' . $church->name);
        }

        $this->command->info('✓ Social Program seeding completed: ' . $totalProgramsCreated . ' programs created');
    }
}
