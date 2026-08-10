<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Church;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data pendukung
        $admin = User::where('email', 'admin@united.com')->first();
        $churchPusat = Church::where('name', 'Gereja United Pusat')->first();
        $churchBandung = Church::where('name', 'Gereja United Bandung')->first();

        // Jika gereja belum ada (safety check), jangan lanjut
        if (!$churchPusat) return;

        $activities = [
            [
                'church_id' => $churchPusat->id,
                'user_id' => $admin->id,
                'title' => 'Workshop Blockchain untuk Pemuda',
                'description' => 'Pelatihan dasar mengenai teknologi blockchain dan pengenalan ekosistem qq serta token $DUIT untuk generasi muda.',
                'type' => 'pelatihan',
                'event_date' => now()->addDays(2),
                'is_published' => true,
            ],
            [
                'church_id' => $churchPusat->id,
                'user_id' => $admin->id,
                'title' => 'Pembagian Sembako Digital',
                'description' => 'Kegiatan rutin bantuan sosial menggunakan sistem QR Code United untuk memastikan distribusi yang tepat sasaran.',
                'type' => 'kegiatan_sosial',
                'event_date' => now()->addDays(5),
                'is_published' => true,
            ],
            [
                'church_id' => $churchBandung->id ?? $churchPusat->id,
                'user_id' => $admin->id,
                'title' => 'Ibadah Raya & Sosialisasi United',
                'content' => 'Ibadah bersama sekaligus pemaparan visi global United dalam membangun jaringan node di seluruh dunia.',
                'type' => 'ibadah',
                'activity_date' => now()->addDays(7),
                'is_published' => true,
            ],
            [
                'church_id' => $churchPusat->id,
                'user_id' => $admin->id,
                'title' => 'Pertemuan Leader Regional',
                'content' => 'Koordinasi antar pemimpin komunitas untuk membahas ekspansi mapping member United secara global.',
                'type' => 'pertemuan',
                'activity_date' => now()->subDays(2), // Kegiatan yang sudah lewat
                'is_published' => true,
            ],
        ];

        foreach ($activities as $data) {
            Activity::updateOrCreate(
                ['title' => $data['title']], 
                $data
            );
        }

        $this->command->info('✅ Activity Seeder berhasil dijalankan!');
    }
}