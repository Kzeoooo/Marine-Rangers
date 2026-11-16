<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrioritasLaporanTest extends TestCase
{
    use RefreshDatabase;

    private User $relawan;
    private User $pemantauKenjeran;
    private User $pemantauKedungCowek;
    private User $admin;
    private Kategori $kategoriJaring;
    private Kategori $kategoriPlastik;
    private Kategori $kategoriOrganik;

    protected function setUp(): void
    {
        parent::setUp();

        $this->relawan = User::create([
            'nama' => 'Budi Relawan',
            'email' => 'budi@relawan.test',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $this->pemantauKenjeran = User::create([
            'nama' => 'Pemantau Kenjeran',
            'email' => 'kenjeran@pemantau.test',
            'password' => 'password123',
            'role' => User::ROLE_PEMANTAU,
            'wilayah_tugas' => 'Pantai Kenjeran',
        ]);

        $this->pemantauKedungCowek = User::create([
            'nama' => 'Pemantau Kedung Cowek',
            'email' => 'kedungcowek@pemantau.test',
            'password' => 'password123',
            'role' => User::ROLE_PEMANTAU,
            'wilayah_tugas' => 'Kedung Cowek',
        ]);

        $this->admin = User::create([
            'nama' => 'Admin DLH',
            'email' => 'admin@dlh.test',
            'password' => 'admin123',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->kategoriJaring = Kategori::create(['nama_kategori' => 'Jaring/Ghost gear']);
        $this->kategoriPlastik = Kategori::create(['nama_kategori' => 'Plastik']);
        $this->kategoriOrganik = Kategori::create(['nama_kategori' => 'Organik']);
    }

    /** @test */
    public function laporan_baru_jaring_dengan_kata_kunci_darurat_otomatis_mendapatkan_prioritas_tinggi()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->relawan)->post('/lapor', [
            'id_kategori' => $this->kategoriJaring->id_kategori,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Ditemukan jaring nelayan terjerat di karang, berpotensi bahaya bagi satwa',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'foto' => UploadedFile::fake()->image('jaring.jpg'),
        ]);

        $response->assertRedirect('/laporan');

        $laporan = Laporan::latest('id_laporan')->first();
        $this->assertNotNull($laporan);
        $this->assertEquals(Laporan::PRIORITAS_TINGGI, $laporan->prioritas);
        $this->assertGreaterThanOrEqual(70, $laporan->skor_prioritas);
    }

    /** @test */
    public function laporan_organik_tanpa_kata_kunci_mendapatkan_prioritas_rendah()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->relawan)->post('/lapor', [
            'id_kategori' => $this->kategoriOrganik->id_kategori,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Sedikit dedaunan kering',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'foto' => UploadedFile::fake()->image('organik.jpg'),
        ]);

        $response->assertRedirect('/laporan');

        $laporan = Laporan::latest('id_laporan')->first();
        $this->assertNotNull($laporan);
        $this->assertEquals(Laporan::PRIORITAS_RENDAH, $laporan->prioritas);
        $this->assertLessThan(40, $laporan->skor_prioritas);
    }

    /** @test */
    public function laporan_plastik_dengan_kata_kunci_volume_menghasilkan_prioritas_sedang()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->relawan)->post('/lapor', [
            'id_kategori' => $this->kategoriPlastik->id_kategori,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Timbunan botol menumpuk di dekat dermaga',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'foto' => UploadedFile::fake()->image('plastik.jpg'),
        ]);

        $response->assertRedirect('/laporan');

        $laporan = Laporan::latest('id_laporan')->first();
        $this->assertNotNull($laporan);
        $this->assertEquals(Laporan::PRIORITAS_SEDANG, $laporan->prioritas);
        $this->assertEquals(50, $laporan->skor_prioritas);
    }

    /** @test */
    public function update_laporan_menghitung_ulang_prioritas_secara_otomatis()
    {
        $laporan = Laporan::create([
            'id_user' => $this->relawan->id_user,
            'id_kategori' => $this->kategoriOrganik->id_kategori,
            'foto' => 'laporan/awal.jpg',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Dedaunan',
            'waktu_laporan' => now('Asia/Jakarta'),
            'status' => Laporan::STATUS_BARU,
            'prioritas' => Laporan::PRIORITAS_RENDAH,
            'skor_prioritas' => 15,
        ]);

        // Relawan mengubah kategori menjadi Jaring dan deskripsi terjerat bahaya
        $response = $this->actingAs($this->relawan)->put('/laporan/' . $laporan->id_laporan, [
            'id_kategori' => $this->kategoriJaring->id_kategori,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Ternyata ada jaring nelayan terjerat sangat bahaya',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
        ]);

        $response->assertRedirect('/laporan');

        $laporan->refresh();
        $this->assertEquals(Laporan::PRIORITAS_TINGGI, $laporan->prioritas);
        $this->assertGreaterThanOrEqual(70, $laporan->skor_prioritas);
    }

    /** @test */
    public function pemantau_dapat_mengkalibrasi_prioritas_pada_wilayah_tugasnya()
    {
        $laporan = Laporan::create([
            'id_user' => $this->relawan->id_user,
            'id_kategori' => $this->kategoriPlastik->id_kategori,
            'foto' => 'laporan/plastik.jpg',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'wilayah' => 'Pantai Kenjeran',
            'waktu_laporan' => now('Asia/Jakarta'),
            'status' => Laporan::STATUS_DIPANTAU,
            'prioritas' => Laporan::PRIORITAS_SEDANG,
        ]);

        $response = $this->actingAs($this->pemantauKenjeran)->post('/laporan/' . $laporan->id_laporan . '/prioritas', [
            'prioritas' => Laporan::PRIORITAS_TINGGI,
        ]);

        $response->assertSessionHas('success');

        $laporan->refresh();
        $this->assertEquals(Laporan::PRIORITAS_TINGGI, $laporan->prioritas);
    }

    /** @test */
    public function pemantau_dilarang_mengkalibrasi_prioritas_di_luar_wilayah_tugasnya()
    {
        $laporanKenjeran = Laporan::create([
            'id_user' => $this->relawan->id_user,
            'id_kategori' => $this->kategoriPlastik->id_kategori,
            'foto' => 'laporan/plastik.jpg',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'wilayah' => 'Pantai Kenjeran',
            'waktu_laporan' => now('Asia/Jakarta'),
            'status' => Laporan::STATUS_DIPANTAU,
            'prioritas' => Laporan::PRIORITAS_SEDANG,
        ]);

        // Pemantau Kedung Cowek mencoba mengubah laporan Pantai Kenjeran
        $response = $this->actingAs($this->pemantauKedungCowek)->post('/laporan/' . $laporanKenjeran->id_laporan . '/prioritas', [
            'prioritas' => Laporan::PRIORITAS_TINGGI,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function masyarakat_dilarang_mengkalibrasi_prioritas_laporan()
    {
        $laporan = Laporan::create([
            'id_user' => $this->relawan->id_user,
            'id_kategori' => $this->kategoriPlastik->id_kategori,
            'foto' => 'laporan/plastik.jpg',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'wilayah' => 'Pantai Kenjeran',
            'waktu_laporan' => now('Asia/Jakarta'),
            'status' => Laporan::STATUS_BARU,
            'prioritas' => Laporan::PRIORITAS_SEDANG,
        ]);

        $response = $this->actingAs($this->relawan)->post('/laporan/' . $laporan->id_laporan . '/prioritas', [
            'prioritas' => Laporan::PRIORITAS_TINGGI,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_dapat_mengkalibrasi_prioritas_di_semua_wilayah()
    {
        $laporan = Laporan::create([
            'id_user' => $this->relawan->id_user,
            'id_kategori' => $this->kategoriPlastik->id_kategori,
            'foto' => 'laporan/plastik.jpg',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'wilayah' => 'Pantai Kenjeran',
            'waktu_laporan' => now('Asia/Jakarta'),
            'status' => Laporan::STATUS_DIPANTAU,
            'prioritas' => Laporan::PRIORITAS_SEDANG,
        ]);

        $response = $this->actingAs($this->admin)->post('/laporan/' . $laporan->id_laporan . '/prioritas', [
            'prioritas' => Laporan::PRIORITAS_TINGGI,
        ]);

        $response->assertSessionHas('success');

        $laporan->refresh();
        $this->assertEquals(Laporan::PRIORITAS_TINGGI, $laporan->prioritas);
    }

    /** @test */
    public function daftar_laporan_dapat_difilter_berdasarkan_prioritas()
    {
        $lapTinggi = Laporan::create([
            'id_user' => $this->relawan->id_user,
            'id_kategori' => $this->kategoriJaring->id_kategori,
            'foto' => 'laporan/tinggi.jpg',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Laporan Darurat Spesial',
            'waktu_laporan' => now('Asia/Jakarta'),
            'status' => Laporan::STATUS_BARU,
            'prioritas' => Laporan::PRIORITAS_TINGGI,
        ]);

        $lapRendah = Laporan::create([
            'id_user' => $this->relawan->id_user,
            'id_kategori' => $this->kategoriOrganik->id_kategori,
            'foto' => 'laporan/rendah.jpg',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Laporan Ringan Daun',
            'waktu_laporan' => now('Asia/Jakarta'),
            'status' => Laporan::STATUS_BARU,
            'prioritas' => Laporan::PRIORITAS_RENDAH,
        ]);

        $response = $this->actingAs($this->relawan)->get('/laporan?prioritas=tinggi');

        $response->assertStatus(200);
        $response->assertSee('Laporan Darurat Spesial');
        $response->assertDontSee('Laporan Ringan Daun');
    }
}
