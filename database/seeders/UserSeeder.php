<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1 Admin
        User::updateOrCreate(
            ['email' => 'admin@marinerangers.test'],
            [
                'nama' => 'Admin Marine Rangers',
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'organisasi' => 'Dinas Lingkungan Hidup Surabaya',
            ]
        );

        // 1 Pemantau
        User::updateOrCreate(
            ['email' => 'pemantau@marinerangers.test'],
            [
                'nama' => 'Koordinator Pemantau',
                'password' => $password,
                'role' => User::ROLE_PEMANTAU,
                'organisasi' => 'Surabaya Peduli Laut',
            ]
        );

        // 2 Masyarakat
        User::updateOrCreate(
            ['email' => 'relawan1@marinerangers.test'],
            [
                'nama' => 'Budi Relawan',
                'password' => $password,
                'role' => User::ROLE_MASYARAKAT,
                'organisasi' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'relawan2@marinerangers.test'],
            [
                'nama' => 'Siti Relawan',
                'password' => $password,
                'role' => User::ROLE_MASYARAKAT,
                'organisasi' => null,
            ]
        );
    }
}

