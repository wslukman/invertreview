<?php

namespace Database\Factories;

use App\Models\Church;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialProgram>
 */
class SocialProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $programTypes = ['pelatihan', 'pemberian_makanan', 'kesehatan', 'pendidikan', 'lainnya'];

        $titles = [
            'Pelatihan Keterampilan Menjahit',
            'Program Pemberian Makanan Bergizi',
            'Klinik Kesehatan Gratis',
            'Beasiswa Pendidikan Anak',
            'Pelatihan Teknologi Informasi',
            'Program Pemberdayaan Perempuan',
            'Sanitasi dan Kebersihan Lingkungan',
            'Konseling Keluarga',
            'Program Literasi Masyarakat',
            'Pelatihan Kewirausahaan',
            'Program Vaksinasi Keluarga',
            'Bantuan Kebutuhan Sekolah',
        ];

        $descriptions = [
            'Program pelatihan keterampilan untuk meningkatkan kemampuan masyarakat.',
            'Program pemberian makanan bergizi untuk anak-anak kurang gizi.',
            'Pelayanan kesehatan gratis dengan pemeriksaan rutin.',
            'Program beasiswa untuk anak-anak berprestasi dari keluarga kurang mampu.',
            'Pelatihan teknologi informasi untuk meningkatkan literasi digital.',
            'Program pemberdayaan ekonomi khusus untuk perempuan.',
            'Program edukasi dan pendampingan kesehatan lingkungan.',
            'Layanan konseling gratis untuk keluarga yang membutuhkan.',
            'Program literasi untuk meningkatkan kemampuan membaca dan menulis.',
            'Pelatihan kewirausahaan untuk pengembangan usaha kecil menengah.',
            'Program vaksinasi dan pemeriksaan kesehatan keluarga.',
            'Pemberian bantuan perlengkapan sekolah untuk anak kurang mampu.',
        ];

        $startDate = $this->faker->dateTimeBetween('now', '+60 days');
        $endDate = $this->faker->dateTimeBetween($startDate, $startDate->modify('+30 days'));

        return [
            'church_id' => Church::inRandomOrder()->first()?->id ?? Church::factory(),
            'created_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'title' => $this->faker->randomElement($titles),
            'description' => $this->faker->randomElement($descriptions),
            'type' => $this->faker->randomElement($programTypes),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'location' => $this->faker->word(),
            'capacity' => $this->faker->numberBetween(20, 200),
            'registered_count' => $this->faker->numberBetween(0, 50),
            'status' => $this->faker->randomElement(['draft', 'active', 'completed', 'cancelled']),
            'image_path' => null,
            'created_at' => $this->faker->dateTime(),
            'updated_at' => now(),
        ];
    }

    /**
     * State untuk program yang aktif
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
        ]);
    }

    /**
     * State untuk program draft
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * State untuk program selesai
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'start_date' => now()->subDays(60),
            'end_date' => now()->subDays(30),
        ]);
    }

    /**
     * State untuk program pelatihan
     */
    public function training(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'pelatihan',
        ]);
    }

    /**
     * State untuk program pemberian makanan
     */
    public function foodProgram(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'pemberian_makanan',
        ]);
    }
}
