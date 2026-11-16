<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiStatistikTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat Kategori Dasar
        Kategori::create(['id_kategori' => 1, 'nama_kategori' => 'Plastik']);
        Kategori::create(['id_kategori' => 2, 'nama_kategori' => 'Organik']);
        Kategori::create(['id_kategori' => 3, 'nama_kategori' => 'Logam']);

        // Buat User
        $user = User::create([
            'nama' => 'Warga Pelapor',
            'email' => 'warga@example.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_MASYARAKAT,
        ]);

        // Buat Laporan Sampel
        Laporan::create([
            'id_user' => $user->id_user,
            'id_kategori' => 1,
            'wilayah' => 'Pantai Kenjeran',
            'latitude' => -7.2275,
            'longitude' => 112.7985,
            'deskripsi' => 'Timbunan kantong plastik dan botol minuman',
            'foto' => 'laporan/sample1.jpg',
            'status' => Laporan::STATUS_BARU,
            'prioritas' => 'tinggi',
            'skor_prioritas' => 85,
            'waktu_laporan' => now()->subDays(1),
        ]);

        Laporan::create([
            'id_user' => $user->id_user,
            'id_kategori' => 2,
            'wilayah' => 'Kedung Cowek',
            'latitude' => -7.2140,
            'longitude' => 112.7870,
            'deskripsi' => 'Ranting dan daun mengotori akar mangrove',
            'foto' => 'laporan/sample2.jpg',
            'status' => Laporan::STATUS_SELESAI,
            'prioritas' => 'rendah',
            'skor_prioritas' => 25,
            'waktu_laporan' => now()->subDays(3),
        ]);

        Laporan::create([
            'id_user' => $user->id_user,
            'id_kategori' => 1,
            'wilayah' => 'Pantai Kenjeran',
            'latitude' => -7.2280,
            'longitude' => 112.7990,
            'deskripsi' => 'Sedotan dan gelas plastik berserakan',
            'foto' => 'laporan/sample3.jpg',
            'status' => Laporan::STATUS_DIPANTAU,
            'prioritas' => 'sedang',
            'skor_prioritas' => 50,
            'waktu_laporan' => now()->subDays(5),
        ]);
    }

    /** @test */
    public function endpoint_api_laporan_mengembalikan_json_dan_geojson()
    {
        $response = $this->getJson('/api/laporan');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total',
                'filters',
                'data' => [
                    '*' => [
                        'id',
                        'wilayah',
                        'kategori' => ['id', 'nama'],
                        'status',
                        'prioritas',
                        'latitude',
                        'longitude',
                        'marker' => ['color', 'label'],
                        'url_detail',
                    ]
                ],
                'geojson' => [
                    'type',
                    'features' => [
                        '*' => [
                            'type',
                            'geometry' => [
                                'type',
                                'coordinates',
                            ],
                            'properties',
                        ]
                    ]
                ]
            ]);

        $this->assertEquals(3, $response->json('total'));
        $this->assertEquals('FeatureCollection', $response->json('geojson.type'));
    }

    /** @test */
    public function endpoint_api_laporan_dapat_difilter_berdasarkan_wilayah_dan_prioritas()
    {
        // Filter wilayah Kenjeran
        $responseWilayah = $this->getJson('/api/laporan?wilayah=Kenjeran');
        $responseWilayah->assertStatus(200);
        $this->assertEquals(2, $responseWilayah->json('total'));

        // Filter prioritas tinggi
        $responsePrioritas = $this->getJson('/api/laporan?prioritas=tinggi');
        $responsePrioritas->assertStatus(200);
        $this->assertEquals(1, $responsePrioritas->json('total'));
        $this->assertEquals('tinggi', $responsePrioritas->json('data.0.prioritas'));

        // Filter kategori Plastik (id 1)
        $responseKategori = $this->getJson('/api/laporan?kategori=1');
        $responseKategori->assertStatus(200);
        $this->assertEquals(2, $responseKategori->json('total'));
    }

    /** @test */
    public function endpoint_api_laporan_menghormati_wilayah_tugas_pemantau()
    {
        $pemantau = User::create([
            'nama' => 'Pemantau Kedung',
            'email' => 'pemantau.kedung@example.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_PEMANTAU,
            'wilayah_tugas' => 'Kedung Cowek',
        ]);

        $response = $this->actingAs($pemantau)->getJson('/api/laporan');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('total'));
        $this->assertEquals('Kedung Cowek', $response->json('data.0.wilayah'));
    }

    /** @test */
    public function endpoint_api_statistik_mengembalikan_analitik_lengkap()
    {
        $response = $this->getJson('/api/statistik');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'ringkasan' => [
                    'total_laporan',
                    'laporan_selesai',
                    'laporan_proses',
                    'laporan_baru',
                    'persentase_selesai',
                    'laporan_minggu_ini',
                    'wilayah_tertinggi',
                    'jumlah_tertinggi',
                ],
                'tren' => [
                    'labels',
                    'total',
                    'selesai',
                ],
                'per_kategori' => [
                    '*' => [
                        'id_kategori',
                        'nama',
                        'total',
                        'persentase',
                        'color',
                    ]
                ],
                'peringkat_wilayah' => [
                    '*' => [
                        'wilayah',
                        'total',
                        'selesai',
                        'belum_selesai',
                        'persentase_selesai',
                        'persentase_dari_total',
                    ]
                ],
                'per_prioritas' => [
                    'tinggi',
                    'sedang',
                    'rendah',
                ],
                'per_status' => [
                    'baru',
                    'dipantau',
                    'ditindaklanjuti',
                    'selesai',
                ],
            ]);

        $this->assertEquals(3, $response->json('ringkasan.total_laporan'));
        $this->assertEquals(1, $response->json('ringkasan.laporan_selesai'));
        $this->assertEquals(33, $response->json('ringkasan.persentase_selesai'));
        $this->assertEquals('Pantai Kenjeran', $response->json('ringkasan.wilayah_tertinggi'));
        $this->assertEquals(2, $response->json('ringkasan.jumlah_tertinggi'));
    }

    /** @test */
    public function halaman_dashboard_statistik_dapat_diakses_dan_menampilkan_komponen()
    {
        $response = $this->get('/statistik');

        $response->assertStatus(200)
            ->assertSee('Dashboard Statistik')
            ->assertSee('Pemantauan Pesisir')
            ->assertSee('Perkembangan Laporan dari Waktu ke Waktu')
            ->assertSee('Komposisi Sampah Laut')
            ->assertSee('Peringkat Wilayah Tingkat Pencemaran Tertinggi')
            ->assertSee('Pantai Kenjeran')
            ->assertSee('Kedung Cowek')
            ->assertSee('GET /api/laporan')
            ->assertSee('GET /api/statistik');
    }
}
