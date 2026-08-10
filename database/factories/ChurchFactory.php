<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Church>
 */
class ChurchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $churchNames = [
            'Gereja Bethel Palembang',
            'Gereja Misi Indonesia',
            'Gereja Kristen Jawa',
            'Gereja Asembli Allah',
            'Gereja Kemah Tabernakel',
            'Gereja Kalisuci',
            'Gereja Pantekosta Indonesia',
            'Gereja Persekutuan Doa',
            'Gereja Kasih Karunia',
            'Gereja Harapan Baru',
        ];

        $streets = [
            'Jalan Sultan Mahmud Badaruddin',
            'Jalan Sriwijaya',
            'Jalan Kapten Anwar Sani',
            'Jalan Jenderal Ahmad Yani',
            'Jalan Merdeka',
            'Jalan Tanjungsari',
            'Jalan Mahoni',
            'Jalan Komo',
            'Jalan Rasa Sayang',
            'Jalan Putri Ayu',
        ];

        $cities = [
            'Seberang Ulu I, Palembang',
            'Seberang Ulu II, Palembang',
            'Ilir Timur I, Palembang',
            'Ilir Timur II, Palembang',
            'Ilir Barat I, Palembang',
            'Ogan Ilir, Palembang',
            'Kertapati, Palembang',
        ];

        $latitude = $this->faker->latitude(-3.5, -3.7);
        $longitude = $this->faker->longitude(104.5, 104.8);

        return [
            'name' => $this->faker->randomElement($churchNames) . ' ' . $this->faker->numberBetween(1, 10),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '0' . $this->faker->numberBetween(711, 856) . $this->faker->numerify('#######'),
            'address' => $this->faker->numerify('No. ##') . ' ' . $this->faker->randomElement($streets),
            'city' => $this->faker->randomElement($cities),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'logo_path' => null,
            'cover_path' => null,
            'description' => $this->faker->sentence(10),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'suspended']),
            'submitted_by' => null,
            'approved_by' => null,
            'approved_at' => $this->faker->dateTime(),
            'rejection_reason' => null,
        ];
    }

    /**
     * State untuk gereja yang disetujui
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => now()->subDays($this->faker->numberBetween(1, 30)),
        ]);
    }

    /**
     * State untuk gereja yang pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_at' => null,
        ]);
    }

    /**
     * State untuk gereja yang di-reject
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }
}
