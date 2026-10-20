<?php

namespace Tests\Feature;

use App\Models\Dashboard;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\LogAktivitas;
use App\Models\Pembersihan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test nilai default kolom pada model dan tabel.
     */
    public function test_default_values_for_user_and_laporan(): void
    {
        $user = User::create([
            'nama' => 'Test User Default',
            'email' => 'default@example.com',
            'password' => 'secret123',
        ]);

        $this->assertEquals(User::ROLE_MASYARAKAT, $user->role);
        $this->assertTrue($user->isMasyarakat());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isPemantau());

        $kategori = Kategori::create([
            'nama_kategori' => 'Kategori Uji',
        ]);

        $laporan = Laporan::create([
            'id_user' => $user->id_user,
            'id_kategori' => $kategori->id_kategori,
            'foto' => 'uploads/test.jpg',
            'latitude' => -7.2275000,
            'longitude' => 112.7985000,
            'wilayah' => 'Pantai Kenjeran',
            'waktu_laporan' => now(),
        ]);

        $this->assertEquals(Laporan::STATUS_BARU, $laporan->status);
        $this->assertTrue($laporan->isBaru());
        $this->assertFalse($laporan->isSelesai());
    }

    /**
     * Test relasi User ke Laporan, Pembersihan, LogAktivitas, Dashboard.
     */
    public function test_user_relationships(): void
    {
        $user = User::factory()->pemantau()->create();
        $kategori = Kategori::factory()->create();

        $laporan = Laporan::factory()->create([
            'id_user' => $user->id_user,
            'id_kategori' => $kategori->id_kategori,
        ]);

        $pembersihan = Pembersihan::factory()->create([
            'id_laporan' => $laporan->id_laporan,
            'id_user' => $user->id_user,
        ]);

        $log = LogAktivitas::create([
            'id_user' => $user->id_user,
            'aktivitas' => 'Login ke sistem',
            'waktu' => now(),
        ]);

        $dashboard = Dashboard::create([
            'id_user' => $user->id_user,
            'periode_mulai' => now()->startOfMonth(),
            'periode_selesai' => now()->endOfMonth(),
            'jumlah_laporan' => 15,
            'tren_laporan' => 'Meningkat',
            'wilayah_prioritas' => 'Pantai Kenjeran',
            'dibuat_pada' => now(),
        ]);

        $this->assertTrue($user->laporan->contains($laporan));
        $this->assertTrue($user->pembersihan->contains($pembersihan));
        $this->assertTrue($user->logAktivitas->contains($log));
        $this->assertTrue($user->dashboard->contains($dashboard));

        $this->assertEquals($user->id_user, $laporan->user->id_user);
        $this->assertEquals($user->id_user, $pembersihan->user->id_user);
        $this->assertEquals($user->id_user, $log->user->id_user);
        $this->assertEquals($user->id_user, $dashboard->user->id_user);
    }

    /**
     * Test relasi Kategori hasMany Laporan dan Laporan belongsTo Kategori.
     */
    public function test_kategori_and_laporan_relationship(): void
    {
        $kategori = Kategori::factory()->create();
        $user = User::factory()->create();

        $laporan = Laporan::factory()->create([
            'id_user' => $user->id_user,
            'id_kategori' => $kategori->id_kategori,
        ]);

        $this->assertTrue($kategori->laporan->contains($laporan));
        $this->assertEquals($kategori->id_kategori, $laporan->kategori->id_kategori);
    }

    /**
     * Test relasi Laporan hasMany Pembersihan dan Pembersihan belongsTo Laporan.
     */
    public function test_laporan_and_pembersihan_relationship(): void
    {
        $laporan = Laporan::factory()->selesai()->create();
        $pemantau = User::factory()->pemantau()->create();

        $pembersihan = Pembersihan::create([
            'id_laporan' => $laporan->id_laporan,
            'id_user' => $pemantau->id_user,
            'tanggal' => now()->toDateString(),
            'deskripsi_hasil' => 'Pembersihan tuntas',
            'foto_hasil' => 'uploads/pembersihan/foto.jpg',
        ]);

        $this->assertTrue($laporan->pembersihan->contains($pembersihan));
        $this->assertEquals($laporan->id_laporan, $pembersihan->laporan->id_laporan);
    }

    /**
     * Test cascading delete: menghapus User ikut menghapus Laporan miliknya.
     */
    public function test_cascading_delete_on_user(): void
    {
        $user = User::factory()->create();
        $kategori = Kategori::factory()->create();

        $laporan = Laporan::factory()->create([
            'id_user' => $user->id_user,
            'id_kategori' => $kategori->id_kategori,
        ]);

        $this->assertDatabaseHas('laporan', ['id_laporan' => $laporan->id_laporan]);

        $user->delete();

        $this->assertDatabaseMissing('laporan', ['id_laporan' => $laporan->id_laporan]);
    }

    /**
     * Test restrict delete: Kategori yang masih dipakai Laporan tidak boleh dihapus.
     */
    public function test_restrict_delete_on_used_kategori(): void
    {
        $kategori = Kategori::factory()->create();
        $user = User::factory()->create();

        Laporan::factory()->create([
            'id_user' => $user->id_user,
            'id_kategori' => $kategori->id_kategori,
        ]);

        $this->expectException(QueryException::class);
        $kategori->delete();
    }

    /**
     * Test Seeder Database menghasilkan data sesuai spesifikasi.
     */
    public function test_database_seeder_counts(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 4 Akun user terdaftar
        $this->assertEquals(4, User::count());
        $this->assertDatabaseHas('users', ['email' => 'admin@marinerangers.test', 'role' => User::ROLE_ADMIN]);
        $this->assertDatabaseHas('users', ['email' => 'pemantau@marinerangers.test', 'role' => User::ROLE_PEMANTAU]);
        $this->assertDatabaseHas('users', ['email' => 'relawan1@marinerangers.test', 'role' => User::ROLE_MASYARAKAT]);
        $this->assertDatabaseHas('users', ['email' => 'relawan2@marinerangers.test', 'role' => User::ROLE_MASYARAKAT]);

        // 6 Kategori sampah
        $this->assertEquals(6, Kategori::count());

        // 100+ Laporan dummy realistis
        $this->assertGreaterThanOrEqual(100, Laporan::count());

        // Laporan selesai memiliki data pembersihan
        $laporanSelesaiCount = Laporan::where('status', Laporan::STATUS_SELESAI)->count();
        $pembersihanCount = Pembersihan::count();
        $this->assertEquals($laporanSelesaiCount, $pembersihanCount);
    }
}

