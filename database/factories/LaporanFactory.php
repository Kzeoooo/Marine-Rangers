<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Laporan>
 */
class LaporanFactory extends Factory
{
    protected $model = Laporan::class;

    /**
     * Titik koordinat acuan pesisir pantai Surabaya
     */
    protected array $coastalLocations = [
        'Pantai Kenjeran' => ['lat' => -7.2275000, 'lng' => 112.7985000],
        'Kedung Cowek' => ['lat' => -7.2140000, 'lng' => 112.7870000],
        'Pantai Ria Kenjeran' => ['lat' => -7.2420000, 'lng' => 112.8020000],
        'Tambak Wedi' => ['lat' => -7.2030000, 'lng' => 112.7750000],
        'Sukolilo Baru' => ['lat' => -7.2340000, 'lng' => 112.7960000],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $locationName = fake()->randomElement(array_keys($this->coastalLocations));
        $center = $this->coastalLocations[$locationName];

        // Sedikit variasi acak sekitar 0.003 - 0.008 derajat (~300m - 800m)
        $latOffset = fake()->randomFloat(7, -0.006, 0.006);
        $lngOffset = fake()->randomFloat(7, -0.006, 0.006);

        return [
            'id_user' => User::factory(),
            'id_kategori' => Kategori::factory(),
            'foto' => 'uploads/laporan/dummy_' . fake()->lexify('??????') . '.jpg',
            'latitude' => round($center['lat'] + $latOffset, 7),
            'longitude' => round($center['lng'] + $lngOffset, 7),
            'wilayah' => $locationName,
            'deskripsi' => fake()->sentence(12),
            'waktu_laporan' => fake()->dateTimeBetween('-30 days', 'now'),
            'status' => fake()->randomElement(Laporan::STATUSES),
        ];
    }

    public function baru(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Laporan::STATUS_BARU,
        ]);
    }

    public function dipantau(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Laporan::STATUS_DIPANTAU,
        ]);
    }

    public function ditindaklanjuti(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Laporan::STATUS_DITINDAKLANJUTI,
        ]);
    }

    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Laporan::STATUS_SELESAI,
        ]);
    }
}

