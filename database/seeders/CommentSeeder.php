<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publishedActivities = Activity::where('published', true)->get();
        $totalCommentsCreated = 0;

        foreach ($publishedActivities as $activity) {
            // Buat 2-5 komentar untuk setiap aktivitas
            $commentCount = rand(2, 5);

            for ($i = 0; $i < $commentCount; $i++) {
                // 60% dari user terdaftar, 40% dari tamu
                if (rand(1, 100) <= 60) {
                    $user = User::inRandomOrder()->first();
                    Comment::factory()->fromUser()->approved()->create([
                        'activity_id' => $activity->id,
                        'user_id' => $user->id,
                    ]);
                } else {
                    // 70% chance untuk di-approve
                    if (rand(1, 100) <= 70) {
                        Comment::factory()->fromGuest()->approved()->create([
                            'activity_id' => $activity->id,
                        ]);
                    } else {
                        Comment::factory()->fromGuest()->create([
                            'activity_id' => $activity->id,
                            'is_approved' => false,
                        ]);
                    }
                }

                $totalCommentsCreated++;
            }

            $this->command->line('  ✓ Created ' . $commentCount . ' comments for activity: ' . $activity->title);
        }

        $this->command->info('✓ Comment seeding completed: ' . $totalCommentsCreated . ' comments created');
    }
}
