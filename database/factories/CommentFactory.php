<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comments = [
            'Acara yang sangat bermanfaat, terima kasih atas pelayanannya.',
            'Semoga Tuhan memberkati setiap pelayanan ini.',
            'Saya sangat tergerak dan ingin ikut serta dalam kegiatan berikutnya.',
            'Pelayanan ini sungguh menyentuh hati. Tuhan memberkati.',
            'Terima kasih telah mengorganisir acara ini dengan baik.',
            'Saya setuju bahwa inisiatif ini sangat diperlukan masyarakat.',
            'Inspiratif dan penuh dengan pembelajaran berharga.',
            'Bolehkah saya bergabung dengan program selanjutnya?',
            'Ini adalah langkah nyata dalam melayani masyarakat.',
            'Saya bangga menjadi bagian dari komunitas ini.',
        ];

        return [
            'activity_id' => Activity::inRandomOrder()->first()?->id ?? Activity::factory(),
            'user_id' => $this->faker->boolean(60) ? User::inRandomOrder()->first()?->id : null,
            'guest_name' => $this->faker->boolean(40) ? $this->faker->name() : null,
            'guest_email' => $this->faker->boolean(40) ? $this->faker->safeEmail() : null,
            'content' => $this->faker->randomElement($comments),
            'is_approved' => $this->faker->boolean(70),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => now(),
        ];
    }

    /**
     * State untuk komentar yang disetujui
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    /**
     * State untuk komentar dari user terdaftar
     */
    public function fromUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'guest_name' => null,
            'guest_email' => null,
        ]);
    }

    /**
     * State untuk komentar dari tamu
     */
    public function fromGuest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'guest_name' => $this->faker->name(),
            'guest_email' => $this->faker->safeEmail(),
        ]);
    }
}
