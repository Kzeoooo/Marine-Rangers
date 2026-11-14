@extends('layouts.app') {{-- Sesuaikan dengan layout utama aplikasi Anda --}}

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">Riwayat & Daftar Laporan</h2>
            <p class="text-muted">Kelola dan pantau status laporan insiden wilayah secara real-time.</p>
        </div>
        @if(auth()->user()->isMasyarakat())
        <div class="col-md-4 text-md-end">
            <a href="{{ route('lapor') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Buat Laporan Baru
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filter untuk Admin / Pemantau --}}
    @if(!auth()->user()->isMasyarakat())
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="baru" {{ request('status') == 'baru' ? 'selected' : '' }}>Baru</option>
                        <option value="dipantau" {{ request('status') == 'dipantau' ? 'selected' : '' }}>Dipantau</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="wilayah" class="form-label">Cari Wilayah</label>
                    <input type="text" name="wilayah" id="wilayah" class="form-control" value="{{ request('wilayah') }}" placeholder="Masukkan nama wilayah...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Tabel Daftar Laporan --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            @if(!auth()->user()->isMasyarakat())
                            <th>Pelapor</th>
                            @endif
                            <th>Kategori</th>
                            <th>Wilayah</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporans as $laporan)
                        <tr>
                            <td>{{ $laporan->id_laporan ?? $laporan->id }}</td>
                            @if(!auth()->user()->isMasyarakat())
                            <td>{{ $laporan->user->name ?? 'Anonim' }}</td>
                            @endif
                            <td>{{ $laporan->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $laporan->wilayah }}</td>
                            <td>{{ \Carbon\Carbon::parse($laporan->waktu_laporan)->format('d M Y, H:i') }}</td>
                            <td>
                                @php
                                    $badgeColor = match($laporan->status) {
                                        'baru' => 'bg-warning text-dark',
                                        'dipantau' => 'bg-info text-dark',
                                        'selesai' => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }}">{{ ucfirst($laporan->status) }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('laporan.show', $laporan->id_laporan ?? $laporan->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data laporan yang tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $laporans->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection