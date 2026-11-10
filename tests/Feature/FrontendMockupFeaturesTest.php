<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Pembersihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FrontendMockupFeaturesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function beranda_dapat_diakses_dan_menampilkan_elemen_mockup_2(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Plastik']);
        $user = User::factory()->create();

        Laporan::create([
            'id_user' => $user->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/test.png',
            'latitude' => -7.2275,
            'longitude' => 112.7985,
            'wilayah' => 'Pantai Kenjeran',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_BARU,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Marine Rangers');
        $response->assertSee('Satu laporan darimu');
        $response->assertSee('Mendukung SDG 14 — Ekosistem Lautan');
        $response->assertSee('LAPORAN TERBARU');
        $response->assertSee('Lapor dalam');
        $response->assertSee('Peta Real-time');
        $response->assertSee('Berbasis Data');
    }

    /** @test */
    public function halaman_peta_menampilkan_3_kartu_statistik_dan_filter_mockup_5(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Plastik']);
        $user = User::factory()->create();

        Laporan::create([
            'id_user' => $user->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/test.png',
            'latitude' => -7.2275,
            'longitude' => 112.7985,
            'wilayah' => 'Pantai Kenjeran',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_BARU,
        ]);

        $response = $this->actingAs($user)->get('/peta');

        $response->assertStatus(200);
        $response->assertSee('Filter');
        $response->assertSee('JENIS SAMPAH');
        $response->assertSee('WILAYAH');
        $response->assertSee('PERIODE');
        $response->assertSee('TOTAL LAPORAN');
        $response->assertSee('WILAYAH PRIORITAS');
        $response->assertSee('SUDAH DITINDAKLANJUTI');
    }

    /** @test */
    public function halaman_detail_laporan_dapat_diakses_dan_menampilkan_stepper_mockup_4(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Plastik']);
        $user = User::factory()->create(['nama' => 'Dian S.']);

        $laporan = Laporan::create([
            'id_user' => $user->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/test.png',
            'latitude' => -7.2297,
            'longitude' => 112.7858,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'titik dekat tangga masuk',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_DITINDAKLANJUTI,
        ]);

        $response = $this->actingAs($user)->get('/laporan/' . $laporan->id_laporan);

        $response->assertStatus(200);
        $response->assertSee('Laporan #' . str_pad($laporan->id_laporan, 4, '0', STR_PAD_LEFT));
        $response->assertSee('Pantai Kenjeran');
        $response->assertSee('Dian S.');
        $response->assertSee('FOTO SEBELUM');
        $response->assertSee('LOKASI');
        $response->assertSee('-7.2297, 112.7858');
        $response->assertSee('Ditindaklanjuti');
    }

    /** @test */
    public function pemantau_dapat_mengonfirmasi_aksi_pembersihan_dan_menandai_selesai(): void
    {
        Storage::fake('public');

        $kategori = Kategori::create(['nama_kategori' => 'Plastik']);
        $masyarakat = User::factory()->create();
        $pemantau = User::factory()->pemantau()->create([
            'organisasi' => 'Surabaya Peduli Laut',
        ]);

        $laporan = Laporan::create([
            'id_user' => $masyarakat->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/test.png',
            'latitude' => -7.2297,
            'longitude' => 112.7858,
            'wilayah' => 'Pantai Kenjeran',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_DITINDAKLANJUTI,
        ]);

        $fileHasil = UploadedFile::fake()->image('bersih.png');

        $response = $this->actingAs($pemantau)->post('/laporan/' . $laporan->id_laporan . '/tindaklanjut', [
            'deskripsi_hasil' => '15 relawan berhasil membersihkan 50kg sampah plastik.',
            'foto_hasil' => $fileHasil,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan status laporan berubah jadi selesai
        $this->assertEquals(Laporan::STATUS_SELESAI, $laporan->fresh()->status);

        // Pastikan tabel pembersihan memiliki data
        $this->assertDatabaseHas('pembersihan', [
            'id_laporan' => $laporan->id_laporan,
            'id_user' => $pemantau->id_user,
            'deskripsi_hasil' => '15 relawan berhasil membersihkan 50kg sampah plastik.',
        ]);

        // Pastikan tercatat di log aktivitas
        $this->assertDatabaseHas('log_aktivitas', [
            'id_user' => $pemantau->id_user,
        ]);
    }

    /** @test */
    public function masyarakat_tidak_boleh_mengubah_status_atau_eksekusi_tindak_lanjut(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Plastik']);
        $masyarakat = User::factory()->create();

        $laporan = Laporan::create([
            'id_user' => $masyarakat->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'laporan/test.png',
            'latitude' => -7.2297,
            'longitude' => 112.7858,
            'wilayah' => 'Pantai Kenjeran',
            'waktu_laporan' => now(),
            'status' => Laporan::STATUS_BARU,
        ]);

        $responseStatus = $this->actingAs($masyarakat)->post('/laporan/' . $laporan->id_laporan . '/status', [
            'status' => Laporan::STATUS_SELESAI,
        ]);
        $responseStatus->assertStatus(403);

        $responseTindakLanjut = $this->actingAs($masyarakat)->post('/laporan/' . $laporan->id_laporan . '/tindaklanjut', [
            'deskripsi_hasil' => 'Uji coba ilegal',
        ]);
        $responseTindakLanjut->assertStatus(403);
    }
}
