<?php

namespace Database\Factories;

use App\Models\Laporan;
use App\Models\Pembersihan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pembersihan>
 */
class PembersihanFactory extends Factory
{
    protected $model = Pembersihan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_laporan' => Laporan::factory()->selesai(),
            'id_user' => User::factory()->pemantau(),
            'tanggal' => fake()->dateTimeBetween('-25 days', 'now')->format('Y-m-d'),
            'deskripsi_hasil' => 'Pembersihan tuntas dilakukan bersama tim relawan. Sampah telah dikumpulkan dan diangkut ke TPA.',
            'foto_hasil' => 'uploads/pembersihan/dummy_bersih_' . fake()->lexify('??????') . '.jpg',
        ];
    }
}

