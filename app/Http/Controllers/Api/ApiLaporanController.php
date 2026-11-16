<?php

namespace App\Http\Controllers\Api;

use App\Helpers\WilayahHelper;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiLaporanController extends Controller
{
    /**
     * Tampilkan data laporan pesisir dalam format JSON dan GeoJSON.
     * Mendukung filter: wilayah, kategori, status, prioritas, periode, dan pencarian q.
     * Menghormati batasan wilayah pemantau yang sedang login.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Laporan::with(['kategori', 'user:id_user,nama']);

        // 1. Batasan Wilayah untuk Pemantau
        if ($user && $user->isPemantau() && !empty($user->wilayah_tugas)) {
            $user->applyWilayahAccess($query);
        } else {
            // Filter wilayah bebas untuk Admin, Masyarakat, atau Publik
            $wilayah = $request->input('wilayah');
            if (!empty($wilayah) && $wilayah !== 'semua') {
                $query->where('wilayah', 'like', '%' . $wilayah . '%');
            }
        }

        // 2. Filter Kategori Sampah
        $kategori = $request->input('kategori');
        if (!empty($kategori)) {
            if (is_array($kategori)) {
                $query->whereIn('id_kategori', $kategori);
            } else {
                $kategoriIds = explode(',', (string) $kategori);
                $query->whereIn('id_kategori', $kategoriIds);
            }
        }

        // 3. Filter Status Laporan
        $status = $request->input('status');
        if (!empty($status) && in_array($status, Laporan::STATUS_LIST, true)) {
            $query->where('status', $status);
        }

        // 4. Filter Tingkat Prioritas
        $prioritas = $request->input('prioritas');
        if (!empty($prioritas) && in_array($prioritas, Laporan::PRIORITAS_LIST, true)) {
            $query->where('prioritas', $prioritas);
        }

        // 5. Filter Periode
        $periode = $request->input('periode');
        if ($periode === '7_hari') {
            $query->where('waktu_laporan', '>=', now()->subDays(7));
        } elseif ($periode === '30_hari') {
            $query->where('waktu_laporan', '>=', now()->subDays(30));
        } elseif ($periode === '1_tahun') {
            $query->where('waktu_laporan', '>=', now()->subYear());
        }

        // 6. Pencarian Teks
        $search = trim($request->input('q', ''));
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('wilayah', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $laporans = $query->orderBy('waktu_laporan', 'desc')->get();

        // Format data marker dan GeoJSON
        $features = [];
        $data = $laporans->map(function ($lap) use (&$features) {
            $prioritas = $lap->prioritas ?? 'sedang';
            $color = match($prioritas) {
                'tinggi' => '#d93829', // Merah
                'sedang' => '#e07a2c', // Oranye
                'rendah' => '#10b981', // Hijau
                default  => '#e07a2c',
            };

            $lat = (float) $lap->latitude;
            $lng = (float) $lap->longitude;

            $item = [
                'id' => $lap->id_laporan,
                'wilayah' => $lap->wilayah,
                'kategori' => [
                    'id' => $lap->id_kategori,
                    'nama' => $lap->kategori->nama_kategori ?? 'Sampah Laut',
                ],
                'status' => $lap->status,
                'prioritas' => $prioritas,
                'skor_prioritas' => $lap->skor_prioritas ?? 50,
                'latitude' => $lat,
                'longitude' => $lng,
                'deskripsi' => $lap->deskripsi,
                'foto_url' => $lap->foto ? asset('storage/' . $lap->foto) : null,
                'pelapor' => $lap->user->nama ?? 'Anonim',
                'waktu_laporan' => $lap->waktu_laporan ? $lap->waktu_laporan->format('Y-m-d H:i:s') : null,
                'waktu_lalu' => $lap->waktu_laporan ? $lap->waktu_laporan->diffForHumans() : '-',
                'marker' => [
                    'color' => $color,
                    'label' => 'Prioritas ' . ucfirst($prioritas),
                ],
                'url_detail' => route('laporan.show', $lap->id_laporan),
            ];

            // Tambahkan ke GeoJSON FeatureCollection
            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$lng, $lat],
                ],
                'properties' => $item,
            ];

            return $item;
        });

        return response()->json([
            'success' => true,
            'total' => $data->count(),
            'filters' => [
                'wilayah' => $request->input('wilayah', 'semua'),
                'kategori' => $request->input('kategori'),
                'status' => $request->input('status'),
                'prioritas' => $request->input('prioritas'),
                'periode' => $request->input('periode', 'semua'),
                'q' => $search,
            ],
            'data' => $data,
            'geojson' => [
                'type' => 'FeatureCollection',
                'features' => $features,
            ],
        ]);
    }
}
