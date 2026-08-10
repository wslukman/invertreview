<?php

namespace Database\Factories;

use App\Models\Church;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activityTypes = ['ibadah', 'kegiatan_sosial'];

        $titles = [
            'Ibadah Raya Minggu Pagi',
            'Pemahaman Alkitab',
            'Doa Malam Bersama',
            'Kumpulan Pemuda',
            'Pembinaan Anak-anak',
            'Bakti Sosial Masyarakat',
            'Kunjungan Jemaat',
            'Persekutuan Doa',
            'Pelajaran Kitab Injil',
            'Latihan Musik Gereja',
            'Acara Silaturahmi',
            'Pelatihan Kepemimpinan',
            'Program Pemberdayaan Masyarakat',
            'Klinik Kesehatan Gratis',
            'Pembagian Sembako',
        ];

        $descriptions = [
            'Acara rutin yang melibatkan seluruh jemaat untuk beribadat bersama.',
            'Sesi pembelajaran mendalam tentang Alkitab dan ajaran Kristen.',
            'Berkumpul dalam persekutuan doa untuk memohon berkat dan perlindungan.',
            'Kegiatan khusus pemuda gereja untuk mempererat silaturahmi.',
            'Program pembinaan karakter dan pendidikan Kristen untuk anak-anak.',
            'Kegiatan sosial untuk membantu masyarakat yang membutuhkan.',
            'Kunjungan pastoral ke rumah jemaat untuk memberikan dukungan spiritual.',
            'Persekutuan doa rutin untuk meningkatkan hubungan dengan Tuhan.',
            'Mempelajari dan mendiskusikan kitab Injil dengan pemandu.',
            'Latihan musik untuk meningkatkan kualitas nyanyian ibadah.',
            'Acara silaturahmi untuk mempererat hubungan antar jemaat.',
            'Program pelatihan kepemimpinan untuk pemimpin gereja.',
            'Program pemberdayaan ekonomi masyarakat setempat.',
            'Pelayanan kesehatan gratis untuk masyarakat umum.',
            'Pembagian bahan pokok kepada masyarakat kurang mampu.',
        ];

        return [
            'church_id' => Church::inRandomOrder()->first()?->id ?? Church::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'title' => $this->faker->randomElement($titles),
            'content' => $this->faker->randomElement($descriptions),
            'type' => $this->faker->randomElement($activityTypes),
            'date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'time' => $this->faker->time('H:i'),
            'location' => $this->faker->word(),
            'image_path' => null,
            'views' => $this->faker->numberBetween(0, 100),
            'published' => $this->faker->boolean(80),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => now(),
        ];
    }

    /**
     * State untuk aktivitas yang dipublikasi
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
        ]);
    }

    /**
     * State untuk aktivitas yang belum dipublikasi
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
        ]);
    }

    /**
     * State untuk aktivitas ibadah
     */
    public function worship(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'ibadah',
        ]);
    }

    /**
     * State untuk aktivitas sosial
     */
    public function social(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'kegiatan_sosial',
        ]);
    }
}
