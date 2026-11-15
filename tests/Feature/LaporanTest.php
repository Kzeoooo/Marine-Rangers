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

        $file = UploadedFile::fake()->image('insiden.png');

        $response = $this->actingAs($masyarakat)->post('/lapor', [
            'id_kategori' => $kategori->id_kategori,
            'wilayah' => 'Pantai Kenjeran',
            'latitude' => -7.2278000,
            'longitude' => 112.7888000,
            'deskripsi' => 'Ditemukan ceceran limbah minyak di sekitar bibir pantai.',
            'foto' => $file,
        ]);

        $response->assertRedirect('/laporan');
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
        $file = UploadedFile::fake()->image('insiden.png');
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

        $file = UploadedFile::fake()->image('insiden.png');

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

        $response->assertRedirect('/laporan');
        $response->assertSessionHas('success');

        $laporan = Laporan::first();
        $this->assertNotNull($laporan);
        $this->assertNotNull($laporan->waktu_laporan);

        // Nilai waktu_laporan bukan nilai lama dari form melainkan dari server
        $this->assertNotEquals('2010-01-01 12:00:00', $laporan->waktu_laporan->format('Y-m-d H:i:s'));
        $this->assertLessThan(60, abs(now('Asia/Jakarta')->timestamp - $laporan->waktu_laporan->timestamp));
    }

    /** @test */
    public function halaman_daftar_laporan_tampil_200_untuk_tiga_role()
    {
        $masyarakat = User::factory()->masyarakat()->create();
        $pemantau = User::factory()->pemantau()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($masyarakat)->get('/laporan')
            ->assertStatus(200)
            ->assertSee('Laporan Saya');

        $this->actingAs($pemantau)->get('/laporan')
            ->assertStatus(200)
            ->assertSee('Tugas Masuk');

        $this->actingAs($admin)->get('/laporan')
            ->assertStatus(200)
            ->assertSee('Kelola Seluruh Laporan');
    }

    /** @test */
    public function masyarakat_hanya_melihat_laporannya_sendiri_di_daftar_laporan()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Plastik']);
        $userA = User::factory()->masyarakat()->create(['nama' => 'Warga A']);
        $userB = User::factory()->masyarakat()->create(['nama' => 'Warga B']);

        $lapA = Laporan::create([
            'id_user' => $userA->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/a.jpg',
            'latitude' => -7.2275,
            'longitude' => 112.7985,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Laporan milik Warga A',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_BARU,
        ]);

        $lapB = Laporan::create([
            'id_user' => $userB->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/b.jpg',
            'latitude' => -7.2140,
            'longitude' => 112.7870,
            'wilayah' => 'Kedung Cowek',
            'deskripsi' => 'Laporan milik Warga B',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_BARU,
        ]);

        $response = $this->actingAs($userA)->get('/laporan');
        $response->assertStatus(200);
        $response->assertSee('Laporan milik Warga A');
        $response->assertDontSee('Laporan milik Warga B');

        $laporansInView = $response->viewData('laporans');
        $this->assertEquals(1, $laporansInView->total());
        $this->assertEquals($lapA->id_laporan, $laporansInView->first()->id_laporan);
    }

    /** @test */
    public function pemantau_tidak_melihat_laporan_berstatus_baru_atau_wilayah_lain()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Plastik']);
        $warga = User::factory()->masyarakat()->create();
        $pemantau = User::factory()->pemantau(wilayahTugas: 'Pantai Kenjeran')->create();

        // 1. Kenjeran - baru (TIDAK boleh dilihat pemantau)
        $lapBaruKenjeran = Laporan::create([
            'id_user' => $warga->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/1.jpg',
            'latitude' => -7.2275,
            'longitude' => 112.7985,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Laporan Baru Kenjeran',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_BARU,
        ]);

        // 2. Kenjeran - dipantau (Boleh dilihat pemantau)
        $lapDipantauKenjeran = Laporan::create([
            'id_user' => $warga->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/2.jpg',
            'latitude' => -7.2275,
            'longitude' => 112.7985,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Laporan Dipantau Kenjeran',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_DIPANTAU,
        ]);

        // 3. Kenjeran - selesai (Boleh dilihat pemantau)
        $lapSelesaiKenjeran = Laporan::create([
            'id_user' => $warga->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/3.jpg',
            'latitude' => -7.2275,
            'longitude' => 112.7985,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Laporan Selesai Kenjeran',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_SELESAI,
        ]);

        // 4. Kedung Cowek - dipantau (TIDAK boleh dilihat pemantau Kenjeran karena beda wilayah)
        $lapDipantauLain = Laporan::create([
            'id_user' => $warga->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/4.jpg',
            'latitude' => -7.2140,
            'longitude' => 112.7870,
            'wilayah' => 'Kedung Cowek',
            'deskripsi' => 'Laporan Dipantau Kedung Cowek',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_DIPANTAU,
        ]);

        $response = $this->actingAs($pemantau)->get('/laporan');
        $response->assertStatus(200);

        // Hanya laporan wilayah Kenjeran berstatus dipantau & selesai yang muncul
        $response->assertSee('Laporan Dipantau Kenjeran');
        $response->assertSee('Laporan Selesai Kenjeran');
        $response->assertDontSee('Laporan Baru Kenjeran');
        $response->assertDontSee('Laporan Dipantau Kedung Cowek');

        $laporansInView = $response->viewData('laporans');
        $this->assertEquals(2, $laporansInView->total());

        // Jika pemantau coba memaksa filter status=baru, status baru tetap tidak muncul
        $responseFilterBaru = $this->actingAs($pemantau)->get('/laporan?status=baru');
        $responseFilterBaru->assertDontSee('Laporan Baru Kenjeran');
    }

    /** @test */
    public function admin_melihat_semua_laporan_dan_dapat_memfilter_status_lengkap()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Plastik']);
        $warga = User::factory()->masyarakat()->create();
        $admin = User::factory()->admin()->create();

        Laporan::create([
            'id_user' => $warga->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/1.jpg',
            'latitude' => -7.2275,
            'longitude' => 112.7985,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Laporan Kenjeran Baru',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_BARU,
        ]);

        Laporan::create([
            'id_user' => $warga->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/2.jpg',
            'latitude' => -7.2140,
            'longitude' => 112.7870,
            'wilayah' => 'Kedung Cowek',
            'deskripsi' => 'Laporan Kedung Dipantau',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_DIPANTAU,
        ]);

        // Admin melihat semua tanpa filter
        $responseAll = $this->actingAs($admin)->get('/laporan');
        $responseAll->assertStatus(200);
        $this->assertEquals(2, $responseAll->viewData('laporans')->total());
        $responseAll->assertSee('Laporan Kenjeran Baru');
        $responseAll->assertSee('Laporan Kedung Dipantau');

        // Admin filter status=baru
        $responseBaru = $this->actingAs($admin)->get('/laporan?status=baru');
        $this->assertEquals(1, $responseBaru->viewData('laporans')->total());
        $responseBaru->assertSee('Laporan Kenjeran Baru');
        $responseBaru->assertDontSee('Laporan Kedung Dipantau');
    }
}
