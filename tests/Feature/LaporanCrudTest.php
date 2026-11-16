<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaporanCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $relawanA;
    private User $relawanB;
    private User $admin;
    private Kategori $kategoriPlastik;
    private Kategori $kategoriJaring;

    protected function setUp(): void
    {
        parent::setUp();

        $this->relawanA = User::create([
            'nama' => 'Relawan A',
            'email' => 'relawan.a@marinerangers.org',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $this->relawanB = User::create([
            'nama' => 'Relawan B',
            'email' => 'relawan.b@marinerangers.org',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $this->admin = User::create([
            'nama' => 'Admin Utama',
            'email' => 'admin@marinerangers.org',
            'password' => 'adminpassword',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->kategoriPlastik = Kategori::create([
            'nama_kategori' => 'Plastik',
        ]);

        $this->kategoriJaring = Kategori::create([
            'nama_kategori' => 'Jaring/Ghost gear',
        ]);
    }

    private function createLaporan(User $user, string $status = Laporan::STATUS_BARU): Laporan
    {
        return Laporan::create([
            'id_user' => $user->id_user,
            'id_kategori' => $this->kategoriPlastik->id_kategori,
            'foto' => 'laporan/dummy.jpg',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'wilayah' => 'Pantai Kenjeran',
            'deskripsi' => 'Timbunan botol plastik di pesisir',
            'waktu_laporan' => now('Asia/Jakarta'),
            'status' => $status,
        ]);
    }

    /** @test */
    public function relawan_dapat_melihat_halaman_edit_laporan_miliknya_yang_masih_baru()
    {
        $laporan = $this->createLaporan($this->relawanA, Laporan::STATUS_BARU);

        $response = $this->actingAs($this->relawanA)->get('/laporan/' . $laporan->id_laporan . '/edit');

        $response->assertStatus(200);
        $response->assertSee('Edit Laporan Insiden');
        $response->assertSee('Pantai Kenjeran');
        $response->assertSee('Timbunan botol plastik di pesisir');
    }

    /** @test */
    public function relawan_tidak_dapat_mengedit_laporan_milik_relawan_lain()
    {
        $laporanMilkRelawanA = $this->createLaporan($this->relawanA, Laporan::STATUS_BARU);

        $response = $this->actingAs($this->relawanB)->get('/laporan/' . $laporanMilkRelawanA->id_laporan . '/edit');
        $response->assertStatus(403);

        $updateResponse = $this->actingAs($this->relawanB)->put('/laporan/' . $laporanMilkRelawanA->id_laporan, [
            'id_kategori' => $this->kategoriJaring->id_kategori,
            'wilayah' => 'Tambak Wedi',
            'latitude' => -7.2100000,
            'longitude' => 112.7900000,
        ]);
        $updateResponse->assertStatus(403);
    }

    /** @test */
    public function relawan_tidak_dapat_mengedit_laporan_yang_sudah_dipantau_atau_selesai()
    {
        $laporanDipantau = $this->createLaporan($this->relawanA, Laporan::STATUS_DIPANTAU);

        $response = $this->actingAs($this->relawanA)->get('/laporan/' . $laporanDipantau->id_laporan . '/edit');
        $response->assertRedirect('/laporan');
        $response->assertSessionHas('error');

        $updateResponse = $this->actingAs($this->relawanA)->put('/laporan/' . $laporanDipantau->id_laporan, [
            'id_kategori' => $this->kategoriJaring->id_kategori,
            'wilayah' => 'Tambak Wedi',
            'latitude' => -7.2100000,
            'longitude' => 112.7900000,
        ]);
        $updateResponse->assertRedirect('/laporan');
        $updateResponse->assertSessionHas('error');
    }

    /** @test */
    public function relawan_dapat_memperbarui_data_laporan_miliknya_yang_masih_baru()
    {
        Storage::fake('public');
        $laporan = $this->createLaporan($this->relawanA, Laporan::STATUS_BARU);

        $newImage = UploadedFile::fake()->image('gambar_baru.png');

        $response = $this->actingAs($this->relawanA)->put('/laporan/' . $laporan->id_laporan, [
            'id_kategori' => $this->kategoriJaring->id_kategori,
            'wilayah' => 'Kedung Cowek Pesisir',
            'deskripsi' => 'Deskripsi sudah diperbarui dengan detail jaring nelayan',
            'latitude' => -7.2300000,
            'longitude' => 112.8000000,
            'foto' => $newImage,
        ]);

        $response->assertRedirect('/laporan');
        $response->assertSessionHas('success');

        $laporan->refresh();
        $this->assertEquals($this->kategoriJaring->id_kategori, $laporan->id_kategori);
        $this->assertEquals('Kedung Cowek Pesisir', $laporan->wilayah);
        $this->assertEquals('Deskripsi sudah diperbarui dengan detail jaring nelayan', $laporan->deskripsi);
        $this->assertEquals('-7.2300000', (string) $laporan->latitude);
        $this->assertEquals('112.8000000', (string) $laporan->longitude);

        $this->assertDatabaseHas('log_aktivitas', [
            'id_user' => $this->relawanA->id_user,
        ]);
    }

    /** @test */
    public function relawan_dapat_menghapus_laporan_miliknya_yang_masih_baru()
    {
        $laporan = $this->createLaporan($this->relawanA, Laporan::STATUS_BARU);

        $response = $this->actingAs($this->relawanA)->delete('/laporan/' . $laporan->id_laporan);

        $response->assertRedirect('/laporan');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('laporan', [
            'id_laporan' => $laporan->id_laporan,
        ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'id_user' => $this->relawanA->id_user,
        ]);
    }

    /** @test */
    public function relawan_tidak_dapat_menghapus_laporan_milik_orang_lain()
    {
        $laporanMilkRelawanA = $this->createLaporan($this->relawanA, Laporan::STATUS_BARU);

        $response = $this->actingAs($this->relawanB)->delete('/laporan/' . $laporanMilkRelawanA->id_laporan);
        $response->assertStatus(403);

        $this->assertDatabaseHas('laporan', [
            'id_laporan' => $laporanMilkRelawanA->id_laporan,
        ]);
    }

    /** @test */
    public function relawan_tidak_dapat_menghapus_laporan_yang_sudah_dipantau_atau_selesai()
    {
        $laporanSelesai = $this->createLaporan($this->relawanA, Laporan::STATUS_SELESAI);

        $response = $this->actingAs($this->relawanA)->delete('/laporan/' . $laporanSelesai->id_laporan);

        $response->assertRedirect('/laporan');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('laporan', [
            'id_laporan' => $laporanSelesai->id_laporan,
        ]);
    }

    /** @test */
    public function admin_dapat_mengedit_dan_menghapus_laporan_status_baru()
    {
        $laporan = $this->createLaporan($this->relawanA, Laporan::STATUS_BARU);

        // Admin buka edit
        $response = $this->actingAs($this->admin)->get('/laporan/' . $laporan->id_laporan . '/edit');
        $response->assertStatus(200);

        // Admin hapus laporan
        $deleteResponse = $this->actingAs($this->admin)->delete('/laporan/' . $laporan->id_laporan);
        $deleteResponse->assertRedirect('/laporan');
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('laporan', [
            'id_laporan' => $laporan->id_laporan,
        ]);
    }
}
