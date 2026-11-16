<?php

namespace App\Http\Controllers;

use App\Helpers\WilayahHelper;
use App\Models\Kategori;
use App\Models\Laporan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatistikController extends Controller
{
    /**
     * Tampilkan halaman Dashboard Statistik Pesisir (Mockup & Analisis Spasial).
     */
    public function index(Request $request): View
    {
        $periode = $request->input('periode', 'semua');
        $wilayahFilter = $request->input('wilayah', 'semua');

        $dataStatistik = $this->getStatistikData($periode, $wilayahFilter);
        $kategoris = Kategori::all();
        $daftarWilayah = WilayahHelper::DAFTAR_SURABAYA;
        $zonaWilayah = WilayahHelper::ZONA_WILAYAH;

        return view('statistik.index', array_merge($dataStatistik, [
            'periode' => $periode,
            'wilayahFilter' => $wilayahFilter,
            'kategoris' => $kategoris,
            'daftarWilayah' => $daftarWilayah,
            'zonaWilayah' => $zonaWilayah,
        ]));
    }

    /**
     * Endpoint API JSON untuk data analitik dan statistik pesisir.
     */
    public function apiStatistik(Request $request): JsonResponse
    {
        $periode = $request->input('periode', 'semua');
        $wilayahFilter = $request->input('wilayah', 'semua');

        $data = $this->getStatistikData($periode, $wilayahFilter);

        return response()->json(array_merge([
            'success' => true,
            'filters' => [
                'periode' => $periode,
                'wilayah' => $wilayahFilter,
            ],
        ], $data));
    }

    /**
     * Hitung metrik analitik statistik laporan, tren waktu, kategori, dan peringkat pencemaran wilayah.
     *
     * @return array<string, mixed>
     */
    protected function getStatistikData(string $periode = 'semua', string $wilayahFilter = 'semua'): array
    {
        $user = auth()->user();
        $query = Laporan::query();

        // 1. Batasan Wilayah untuk Pemantau
        if ($user && $user->isPemantau() && !empty($user->wilayah_tugas)) {
            $user->applyWilayahAccess($query);
        } elseif (!empty($wilayahFilter) && $wilayahFilter !== 'semua') {
            $query->where('wilayah', 'like', '%' . $wilayahFilter . '%');
        }

        // 2. Filter Periode
        if ($periode === '7_hari') {
            $query->where('waktu_laporan', '>=', now()->subDays(7));
        } elseif ($periode === '30_hari') {
            $query->where('waktu_laporan', '>=', now()->subDays(30));
        } elseif ($periode === '1_tahun') {
            $query->where('waktu_laporan', '>=', now()->subYear());
        }

        $allReports = (clone $query)->with('kategori')->get();
        $totalLaporan = $allReports->count();

        // 3. Ringkasan Status & Prioritas
        $selesaiCount = $allReports->where('status', Laporan::STATUS_SELESAI)->count();
        $ditindaklanjutiCount = $allReports->where('status', Laporan::STATUS_DITINDAKLANJUTI)->count();
        $dipantauCount = $allReports->where('status', Laporan::STATUS_DIPANTAU)->count();
        $baruCount = $allReports->where('status', Laporan::STATUS_BARU)->count();
        $prosesCount = $ditindaklanjutiCount + $dipantauCount;

        $persentaseSelesai = $totalLaporan > 0 ? round(($selesaiCount / $totalLaporan) * 100) : 0;
        $laporanMingguIni = $allReports->filter(function ($item) {
            return $item->waktu_laporan && $item->waktu_laporan->gte(now()->subDays(7));
        })->count();

        // Distribusi Prioritas
        $prioritasTinggi = $allReports->where('prioritas', 'tinggi')->count();
        $prioritasSedang = $allReports->where('prioritas', 'sedang')->count();
        $prioritasRendah = $allReports->where('prioritas', 'rendah')->count();

        // 4. Perkembangan Laporan dari Waktu ke Waktu (Tren)
        $tren = $this->buildTrenData($allReports, $periode);

        // 5. Sebaran per Kategori Sampah
        $kategoriGrouped = $allReports->groupBy('id_kategori');
        $allKategoris = Kategori::all()->keyBy('id_kategori');

        $kategoriPalette = [
            'Plastik' => '#0284c7',   // Sky
            'Organik' => '#10b981',   // Emerald
            'Logam'   => '#f59e0b',   // Amber
            'B3'      => '#ef4444',   // Rose
            'Kaca'    => '#8b5cf6',   // Violet
            'Kertas'  => '#64748b',   // Slate
        ];

        $perKategori = [];
        foreach ($allKategoris as $kId => $kat) {
            $count = isset($kategoriGrouped[$kId]) ? $kategoriGrouped[$kId]->count() : 0;
            $persen = $totalLaporan > 0 ? round(($count / $totalLaporan) * 100, 1) : 0;
            $color = $kategoriPalette[$kat->nama_kategori] ?? '#0ea5e9';

            $perKategori[] = [
                'id_kategori' => $kat->id_kategori,
                'nama' => $kat->nama_kategori,
                'total' => $count,
                'persentase' => $persen,
                'color' => $color,
            ];
        }
        usort($perKategori, fn($a, $b) => $b['total'] <=> $a['total']);

        // 6. Peringkat Wilayah dengan Tingkat Pencemaran Tertinggi
        $wilayahGrouped = $allReports->groupBy('wilayah');
        $peringkatWilayah = [];

        foreach ($wilayahGrouped as $wilayahNama => $items) {
            $totalWilayah = $items->count();
            $selesaiWilayah = $items->where('status', Laporan::STATUS_SELESAI)->count();
            $belumSelesaiWilayah = $totalWilayah - $selesaiWilayah;
            $persenDariTotal = $totalLaporan > 0 ? round(($totalWilayah / $totalLaporan) * 100, 1) : 0;

            $peringkatWilayah[] = [
                'wilayah' => $wilayahNama ?: 'Pesisir Lainnya',
                'total' => $totalWilayah,
                'selesai' => $selesaiWilayah,
                'belum_selesai' => $belumSelesaiWilayah,
                'persentase_selesai' => $totalWilayah > 0 ? round(($selesaiWilayah / $totalWilayah) * 100) : 0,
                'persentase_dari_total' => $persenDariTotal,
            ];
        }
        usort($peringkatWilayah, fn($a, $b) => $b['total'] <=> $a['total']);
        $peringkatWilayahTop = array_slice($peringkatWilayah, 0, 10);

        return [
            'ringkasan' => [
                'total_laporan' => $totalLaporan,
                'laporan_selesai' => $selesaiCount,
                'laporan_proses' => $prosesCount,
                'laporan_baru' => $baruCount,
                'persentase_selesai' => $persentaseSelesai,
                'laporan_minggu_ini' => $laporanMingguIni,
                'wilayah_tertinggi' => $peringkatWilayahTop[0]['wilayah'] ?? 'Belum ada data',
                'jumlah_tertinggi' => $peringkatWilayahTop[0]['total'] ?? 0,
            ],
            'tren' => $tren,
            'per_kategori' => $perKategori,
            'peringkat_wilayah' => $peringkatWilayahTop,
            'per_prioritas' => [
                'tinggi' => $prioritasTinggi,
                'sedang' => $prioritasSedang,
                'rendah' => $prioritasRendah,
            ],
            'per_status' => [
                'baru' => $baruCount,
                'dipantau' => $dipantauCount,
                'ditindaklanjuti' => $ditindaklanjutiCount,
                'selesai' => $selesaiCount,
            ],
        ];
    }

    /**
     * Membangun data time series untuk grafik tren perkembangan laporan.
     *
     * @param \Illuminate\Support\Collection<int, Laporan> $laporans
     * @param string $periode
     * @return array{labels: array<string>, total: array<int>, selesai: array<int>}
     */
    protected function buildTrenData($laporans, string $periode): array
    {
        $labels = [];
        $totalData = [];
        $selesaiData = [];

        if ($periode === '7_hari') {
            // Harian 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $key = $date->format('Y-m-d');
                $labels[] = $date->translatedFormat('d M');

                $dayReports = $laporans->filter(fn($lap) => $lap->waktu_laporan && $lap->waktu_laporan->format('Y-m-d') === $key);
                $totalData[] = $dayReports->count();
                $selesaiData[] = $dayReports->where('status', Laporan::STATUS_SELESAI)->count();
            }
        } elseif ($periode === '30_hari') {
            // Dikelompokkan per 5 hari untuk 30 hari terakhir
            for ($i = 29; $i >= 0; $i -= 5) {
                $start = now()->subDays($i);
                $end = now()->subDays(max(0, $i - 4));
                $label = $start->translatedFormat('d M');
                $labels[] = $label;

                $bucketReports = $laporans->filter(function ($lap) use ($start, $end) {
                    if (!$lap->waktu_laporan) return false;
                    return $lap->waktu_laporan->between($start->startOfDay(), $end->endOfDay());
                });

                $totalData[] = $bucketReports->count();
                $selesaiData[] = $bucketReports->where('status', Laporan::STATUS_SELESAI)->count();
            }
        } else {
            // Default 6 bulan terakhir
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthYear = $date->format('Y-m');
                $labels[] = $date->translatedFormat('M Y');

                $monthReports = $laporans->filter(fn($lap) => $lap->waktu_laporan && $lap->waktu_laporan->format('Y-m') === $monthYear);
                $totalData[] = $monthReports->count();
                $selesaiData[] = $monthReports->where('status', Laporan::STATUS_SELESAI)->count();
            }
        }

        return [
            'labels' => $labels,
            'total' => $totalData,
            'selesai' => $selesaiData,
        ];
    }
}
