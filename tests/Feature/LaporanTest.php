<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Laporan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function masyarakat_dapat_mengakses_halaman_lapor()
    {
        $masyarakat = User::create([
            'nama' => 'Warga Pantai',
            'email' => 'warga@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $this->actingAs($masyarakat)
             ->get('/lapor')
             ->assertStatus(200);
    }

    /** @test */
    public function masyarakat_dapat_mengirim_laporan_baru()
    {
        Storage::fake('public');

        $masyarakat = User::create([
            'nama' => 'Warga Pelapor',
            'email' => 'pelapor@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $file = UploadedFile::fake()->image('insiden.jpg');

        $response = $this->actingAs($masyarakat)->post('/lapor', [
            'wilayah' => 'Pantai Kenjeran',
            'latitude' => -7.2278000,
            'longitude' => 112.7888000,
            'waktu_laporan' => now()->format('Y-m-d\TH:i'),
            'deskripsi' => 'Ditemukan ceceran limbah minyak di sekitar bibir pantai.',
            'foto' => $file,
        ]);

        $response->assertRedirect('/lapor');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('laporan', [
            'id_user' => $masyarakat->id_user,
            'wilayah' => 'Pantai Kenjeran',
            'status' => Laporan::STATUS_BARU,
        ]);
    }
}