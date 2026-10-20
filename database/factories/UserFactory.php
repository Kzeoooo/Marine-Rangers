<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => User::ROLE_MASYARAKAT,
            'organisasi' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * State: Admin
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
            'organisasi' => 'Dinas Lingkungan Hidup Surabaya',
        ]);
    }

    /**
     * State: Pemantau
     */
    public function pemantau(?string $organisasi = 'Surabaya Peduli Laut'): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_PEMANTAU,
            'organisasi' => $organisasi,
        ]);
    }

    /**
     * State: Masyarakat
     */
    public function masyarakat(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_MASYARAKAT,
            'organisasi' => null,
        ]);
    }
}
