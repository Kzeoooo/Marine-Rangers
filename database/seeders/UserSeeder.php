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

        // 1. Admin DLH Surabaya
        User::updateOrCreate(
            ['email' => 'admin@marinerangers.test'],
            [
                'nama' => 'Admin Marine Rangers',
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'organisasi' => 'Dinas Lingkungan Hidup Surabaya',
                'wilayah_tugas' => null,
            ]
        );

        // 2. Pemantau per Wilayah
        $pemantauList = [
            [
                'email' => 'pemantau@marinerangers.test',
                'nama' => 'Koordinator Pemantau (Kenjeran)',
                'organisasi' => 'Surabaya Peduli Laut',
                'wilayah_tugas' => 'Pantai Kenjeran',
            ],
            [
                'email' => 'pemantau.kenjeran@marinerangers.test',
                'nama' => 'Pemantau Wilayah Kenjeran',
                'organisasi' => 'Pokdarwis Kenjeran Bersih',
                'wilayah_tugas' => 'Pantai Kenjeran',
            ],
            [
                'email' => 'pemantau.kedungcowek@marinerangers.test',
                'nama' => 'Pemantau Kedung Cowek',
                'organisasi' => 'Forum Relawan Mangrove Kedung Cowek',
                'wilayah_tugas' => 'Kedung Cowek',
            ],
            [
                'email' => 'pemantau.tambakwedi@marinerangers.test',
                'nama' => 'Pemantau Tambak Wedi',
                'organisasi' => 'Komunitas Pesisir Tambak Wedi Suramadu',
                'wilayah_tugas' => 'Tambak Wedi',
            ],
            [
                'email' => 'pemantau.pantairia@marinerangers.test',
                'nama' => 'Pemantau Pantai Ria Kenjeran',
                'organisasi' => 'Sahabat Lingkungan Pantai Ria',
                'wilayah_tugas' => 'Pantai Ria Kenjeran',
            ],
            [
                'email' => 'pemantau.sukolilo@marinerangers.test',
                'nama' => 'Pemantau Sukolilo Baru',
                'organisasi' => 'Laskar Bahari Nelayan Sukolilo',
                'wilayah_tugas' => 'Sukolilo Baru',
            ],
        ];

        foreach ($pemantauList as $p) {
            User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'nama' => $p['nama'],
                    'password' => $password,
                    'role' => User::ROLE_PEMANTAU,
                    'organisasi' => $p['organisasi'],
                    'wilayah_tugas' => $p['wilayah_tugas'],
                ]
            );
        }

        // 3. Relawan / Masyarakat
        User::updateOrCreate(
            ['email' => 'relawan1@marinerangers.test'],
            [
                'nama' => 'Budi Relawan',
                'password' => $password,
                'role' => User::ROLE_MASYARAKAT,
                'organisasi' => null,
                'wilayah_tugas' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'relawan2@marinerangers.test'],
            [
                'nama' => 'Siti Relawan',
                'password' => $password,
                'role' => User::ROLE_MASYARAKAT,
                'organisasi' => null,
                'wilayah_tugas' => null,
            ]
        );
    }
}
