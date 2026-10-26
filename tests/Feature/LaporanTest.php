<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
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

        $kategori = Kategori::create([
            'nama_kategori' => 'Limbah Minyak',
        ]);

        $file = UploadedFile::fake()->image('insiden.jpg');

        $response = $this->actingAs($masyarakat)->post('/lapor', [
            'id_kategori' => $kategori->id_kategori,
            'wilayah' => 'Pantai Kenjeran',
            'latitude' => -7.2278000,
            'longitude' => 112.7888000,
            'deskripsi' => 'Ditemukan ceceran limbah minyak di sekitar bibir pantai.',
            'foto' => $file,
        ]);

        $response->assertRedirect('/lapor');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('laporan', [
            'id_user' => $masyarakat->id_user,
            'id_kategori' => $kategori->id_kategori,
            'wilayah' => 'Pantai Kenjeran',
            'status' => Laporan::STATUS_BARU,
        ]);
    }

    /** @test */
    public function laporan_tanpa_foto_atau_kategori_ditolak()
    {
        Storage::fake('public');

        $masyarakat = User::create([
            'nama' => 'Warga Pelapor Uji',
            'email' => 'pelapor_uji@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $kategori = Kategori::create([
            'nama_kategori' => 'Sampah Plastik',
        ]);

        // 1. Pengiriman tanpa foto harus ditolak
        $responseTanpaFoto = $this->actingAs($masyarakat)->post('/lapor', [
            'id_kategori' => $kategori->id_kategori,
            'wilayah' => 'Pantai Kenjeran',
            'latitude' => -7.2278000,
            'longitude' => 112.7888000,
            'deskripsi' => 'Laporan uji tanpa foto',
        ]);

        $responseTanpaFoto->assertSessionHasErrors(['foto']);

        // 2. Pengiriman tanpa id_kategori harus ditolak
        $file = UploadedFile::fake()->image('insiden.jpg');
        $responseTanpaKategori = $this->actingAs($masyarakat)->post('/lapor', [
            'wilayah' => 'Pantai Kenjeran',
            'latitude' => -7.2278000,
            'longitude' => 112.7888000,
            'deskripsi' => 'Laporan uji tanpa kategori',
            'foto' => $file,
        ]);

        $responseTanpaKategori->assertSessionHasErrors(['id_kategori']);

        // Pastikan tidak ada data laporan tersimpan ke database
        $this->assertDatabaseCount('laporan', 0);
    }

    /** @test */
    public function waktu_laporan_diisi_otomatis_oleh_server()
    {
        Storage::fake('public');

        $masyarakat = User::create([
            'nama' => 'Warga Waktu',
            'email' => 'warga_waktu@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $kategori = Kategori::create([
            'nama_kategori' => 'Pencemaran Air',
        ]);

        $file = UploadedFile::fake()->image('insiden.jpg');

        // Pengguna mengirimkan waktu_laporan palsu di masa lalu
        $response = $this->actingAs($masyarakat)->post('/lapor', [
            'id_kategori' => $kategori->id_kategori,
            'wilayah' => 'Pantai Kenjeran',
            'latitude' => -7.2278000,
            'longitude' => 112.7888000,
            'deskripsi' => 'Laporan uji waktu otomatis',
            'foto' => $file,
            'waktu_laporan' => '2010-01-01 12:00:00',
        ]);

        $response->assertRedirect('/lapor');
        $response->assertSessionHas('success');

        $laporan = Laporan::first();
        $this->assertNotNull($laporan);
        $this->assertNotNull($laporan->waktu_laporan);

        // Nilai waktu_laporan bukan nilai lama dari form melainkan dari server
        $this->assertNotEquals('2010-01-01 12:00:00', $laporan->waktu_laporan->format('Y-m-d H:i:s'));
        $this->assertLessThan(60, abs(now('Asia/Jakarta')->timestamp - $laporan->waktu_laporan->timestamp));
    }
}
