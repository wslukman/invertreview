<?php

namespace Database\Seeders;

use App\Models\Church;
use Illuminate\Database\Seeder;

class ChurchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat 5 gereja yang sudah disetujui
        Church::factory(5)->approved()->create();

        // Buat 3 gereja yang masih pending
        Church::factory(3)->pending()->create();

        // Buat 2 gereja yang di-reject
        Church::factory(2)->rejected()->create();

        $this->command->info('✓ Church seeding completed: 10 churches created');
    }
}
