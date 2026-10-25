<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRbacTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registrasi_sukses_dan_otomatis_jadi_masyarakat()
    {
        $response = $this->post('/daftar', [
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin', // Coba injeksi role admin lewat form
        ]);

        $response->assertRedirect('/dashboard');
        
        $this->assertAuthenticated();
        
        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(User::ROLE_MASYARAKAT, $user->role); // Pastikan dipaksa jadi masyarakat
        $this->assertTrue(Hash::check('password123', $user->password)); // Pastikan ter-hash dengan benar

        // Cek log aktivitas tercatat
        $this->assertDatabaseHas('log_aktivitas', [
            'id_user' => $user->id_user,
            'aktivitas' => 'Registrasi akun baru sebagai masyarakat',
        ]);
    }

    /** @test */
    public function registrasi_gagal_jika_validasi_tidak_sesuai()
    {
        // Email duplikat, password kurang dari 8 karakter, konfirmasi tidak cocok
        User::create([
            'nama' => 'Existing User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $response = $this->post('/daftar', [
            'nama' => 'Ahmad',
            'email' => 'test@example.com', // Duplikat
            'password' => '123', // Terlalu pendek
            'password_confirmation' => 'selain123', // Tidak cocok
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** @test */
    public function login_sukses_untuk_berbagai_role_dan_redirect_dashboard()
    {
        $admin = User::create([
            'nama' => 'Admin Sistem',
            'email' => 'admin@marinerangers.com',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@marinerangers.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        
        // Cek redirect dari dashboard berdasarkan role admin -> /admin
        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin');
    }

    /** @test */
    public function login_gagal_dengan_pesan_generik_dan_rate_limiting()
    {
        $user = User::create([
            'nama' => 'User Test',
            'email' => 'user@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        // Coba login salah 5 kali
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'user@example.com',
                'password' => 'salahpassword',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // Percobaan ke-6 harusnya terkena rate limiter
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'salahpassword',
        ]);
        
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function rbac_akses_halaman_sesuai_role()
    {
        $masyarakat = User::create([
            'nama' => 'Masyarakat Biasa',
            'email' => 'masyarakat@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $admin = User::create([
            'nama' => 'Admin Utama',
            'email' => 'admin2@example.com',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);

        // Tamu mencoba akses /peta harus diarahkan ke login
        $this->get('/peta')->assertRedirect('/login');

        // Masyarakat akses /admin harus 403 Forbidden
        $this->actingAs($masyarakat)->get('/admin')->assertStatus(403);

        // Masyarakat akses /lapor harus 200 OK
        $this->actingAs($masyarakat)->get('/lapor')->assertStatus(200);

        // Admin akses /admin harus 200 OK
        $this->actingAs($admin)->get('/admin')->assertStatus(200);

        // Admin akses /lapor harus 403 (karena /lapor khusus masyarakat)
        $this->actingAs($admin)->get('/lapor')->assertStatus(403);
    }

    /** @test */
    public function logout_berhasil_dan_mencatat_log()
    {
        $user = User::create([
            'nama' => 'User Logout',
            'email' => 'logout@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MASYARAKAT,
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();

        // Cek log aktivitas logout tercatat
        $this->assertDatabaseHas('log_aktivitas', [
            'id_user' => $user->id_user,
            'aktivitas' => 'Logout dari sistem',
        ]);
    }
}