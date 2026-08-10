<?php

namespace Database\Factories;

use App\Models\SocialProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramRegistration>
 */
class ProgramRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => SocialProgram::inRandomOrder()->first()?->id ?? SocialProgram::factory(),
            'user_id' => $this->faker->boolean(60) ? User::inRandomOrder()->first()?->id : null,
            'guest_name' => $this->faker->boolean(40) ? $this->faker->name() : null,
            'guest_email' => $this->faker->boolean(40) ? $this->faker->safeEmail() : null,
            'guest_phone' => $this->faker->boolean(40) ? '0' . $this->faker->numberBetween(711, 856) . $this->faker->numerify('#######') : null,
            'status' => $this->faker->randomElement(['registered', 'attended', 'cancelled']),
            'attended_at' => $this->faker->boolean(50) ? $this->faker->dateTime() : null,
            'created_at' => $this->faker->dateTime(),
            'updated_at' => now(),
        ];
    }

    /**
     * State untuk registrasi dari user terdaftar
     */
    public function fromUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'guest_name' => null,
            'guest_email' => null,
            'guest_phone' => null,
        ]);
    }

    /**
     * State untuk registrasi dari tamu
     */
    public function fromGuest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'guest_name' => $this->faker->name(),
            'guest_email' => $this->faker->safeEmail(),
            'guest_phone' => '0' . $this->faker->numberBetween(711, 856) . $this->faker->numerify('#######'),
        ]);
    }

    /**
     * State untuk registrasi yang sudah hadir
     */
    public function attended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'attended',
            'attended_at' => now(),
        ]);
    }
}
