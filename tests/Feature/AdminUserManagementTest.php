<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(array $overrides = []): User
    {
        return User::create(array_merge([
            'nama' => 'Admin Utama',
            'email' => 'admin@marinerangers.org',
            'password' => 'adminpassword',
            'role' => User::ROLE_ADMIN,
            'organisasi' => 'Dinas Lingkungan Hidup Surabaya',
        ], $overrides));
    }

    private function createPemantau(array $overrides = []): User
    {
        return User::create(array_merge([
            'nama' => 'Pemantau Kenjeran',
            'email' => 'pemantau.kenjeran@marinerangers.org',
            'password' => 'pemantau123',
            'role' => User::ROLE_PEMANTAU,
            'organisasi' => 'Komunitas Peduli Pesisir',
            'wilayah_tugas' => 'Pantai Kenjeran',
        ], $overrides));
    }

    private function createMasyarakat(array $overrides = []): User
    {
        return User::create(array_merge([
            'nama' => 'Warga Pesisir',
            'email' => 'warga@gmail.com',
            'password' => 'warga12345',
            'role' => User::ROLE_MASYARAKAT,
        ], $overrides));
    }

    /** @test */
    public function tamu_diarahkan_ke_halaman_login()
    {
        $response = $this->get('/admin/pengguna');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function non_admin_dilarang_mengakses_halaman_kelola_pengguna()
    {
        $pemantau = $this->createPemantau();
        $masyarakat = $this->createMasyarakat();

        $this->actingAs($pemantau)->get('/admin/pengguna')->assertStatus(403);
        $this->actingAs($masyarakat)->get('/admin/pengguna')->assertStatus(403);
    }

    /** @test */
    public function admin_dapat_mengakses_halaman_kelola_pengguna_dan_melihat_statistik()
    {
        $admin = $this->createAdmin();
        $pemantau = $this->createPemantau();
        $masyarakat = $this->createMasyarakat();

        $response = $this->actingAs($admin)->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Pengguna');
        $response->assertSee($admin->nama);
        $response->assertSee($pemantau->nama);
        $response->assertSee($masyarakat->nama);
        $response->assertSee('Pantai Kenjeran');
    }

    /** @test */
    public function admin_dapat_mencari_dan_memfilter_pengguna_berdasarkan_peran()
    {
        $admin = $this->createAdmin();
        $pemantau = $this->createPemantau(['nama' => 'Petugas Khusus Sukolilo', 'email' => 'sukolilo@mr.org', 'wilayah_tugas' => 'Sukolilo Baru']);
        $masyarakat = $this->createMasyarakat(['nama' => 'Warga Biasa', 'email' => 'biasa@gmail.com']);

        // Filter berdasarkan peran 'pemantau'
        $response = $this->actingAs($admin)->get('/admin/pengguna?role=pemantau');
        $response->assertStatus(200);
        $response->assertSee('Petugas Khusus Sukolilo');
        $response->assertDontSee('Warga Biasa');

        // Pencarian dengan keyword 'Sukolilo'
        $searchResponse = $this->actingAs($admin)->get('/admin/pengguna?q=Sukolilo');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Petugas Khusus Sukolilo');
        $searchResponse->assertDontSee('Warga Biasa');
    }

    /** @test */
    public function admin_dapat_menambah_pengguna_baru_pemantau_dengan_wilayah_tugas()
    {
        $admin = $this->createAdmin();

        $payload = [
            'nama' => 'Pemantau Tambak Wedi',
            'email' => 'pemantau.tambakwedi@marinerangers.org',
            'password' => 'password123',
            'role' => User::ROLE_PEMANTAU,
            'organisasi' => 'Ranger Pesisir Timur',
            'wilayah_tugas' => 'Tambak Wedi',
        ];

        $response = $this->actingAs($admin)->post('/admin/pengguna', $payload);

        $response->assertRedirect('/admin/pengguna');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'pemantau.tambakwedi@marinerangers.org',
            'role' => User::ROLE_PEMANTAU,
            'wilayah_tugas' => 'Tambak Wedi',
            'organisasi' => 'Ranger Pesisir Timur',
        ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'id_user' => $admin->id_user,
        ]);
    }

    /** @test */
    public function admin_dapat_memperbarui_data_pengguna_dan_wilayah_tugas()
    {
        $admin = $this->createAdmin();
        $targetUser = $this->createPemantau([
            'nama' => 'Pemantau Awal',
            'email' => 'lama@example.com',
            'wilayah_tugas' => 'Pantai Kenjeran',
        ]);

        $updatePayload = [
            'nama' => 'Pemantau Terkini',
            'email' => 'baru@example.com',
            'role' => User::ROLE_PEMANTAU,
            'organisasi' => 'Kelompok Pengawas Masyarakat',
            'wilayah_tugas' => 'Kedung Cowek',
        ];

        $response = $this->actingAs($admin)->put('/admin/pengguna/' . $targetUser->id_user, $updatePayload);

        $response->assertRedirect('/admin/pengguna');
        $response->assertSessionHas('success');

        $targetUser->refresh();
        $this->assertEquals('Pemantau Terkini', $targetUser->nama);
        $this->assertEquals('baru@example.com', $targetUser->email);
        $this->assertEquals('Kedung Cowek', $targetUser->wilayah_tugas);
    }

    /** @test */
    public function admin_tidak_dapat_menghapus_akunnya_sendiri()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->delete('/admin/pengguna/' . $admin->id_user);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id_user' => $admin->id_user,
        ]);
    }

    /** @test */
    public function admin_tidak_dapat_mendegradasi_peran_akunnya_sendiri()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->put('/admin/pengguna/' . $admin->id_user, [
            'nama' => $admin->nama,
            'email' => $admin->email,
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $response->assertSessionHas('error');
        $admin->refresh();
        $this->assertEquals(User::ROLE_ADMIN, $admin->role);
    }

    /** @test */
    public function admin_dapat_menghapus_akun_pengguna_lain()
    {
        $admin = $this->createAdmin();
        $warga = $this->createMasyarakat(['nama' => 'Warga Yang Mau Dihapus']);

        $response = $this->actingAs($admin)->delete('/admin/pengguna/' . $warga->id_user);

        $response->assertRedirect('/admin/pengguna');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id_user' => $warga->id_user,
        ]);
    }

    /** @test */
    public function validasi_gagal_jika_email_duplikat_atau_password_kurang_dari_8_karakter()
    {
        $admin = $this->createAdmin();
        $this->createMasyarakat(['email' => 'ada@example.com']);

        // Test email duplikat
        $responseDuplicate = $this->actingAs($admin)->post('/admin/pengguna', [
            'nama' => 'User Baru',
            'email' => 'ada@example.com',
            'password' => 'validpassword123',
            'role' => User::ROLE_MASYARAKAT,
        ]);
        $responseDuplicate->assertSessionHasErrors(['email']);

        // Test password kurang dari 8 karakter
        $responseShortPassword = $this->actingAs($admin)->post('/admin/pengguna', [
            'nama' => 'User Baru 2',
            'email' => 'unik@example.com',
            'password' => 'pendek',
            'role' => User::ROLE_MASYARAKAT,
        ]);
        $responseShortPassword->assertSessionHasErrors(['password']);
    }
}
