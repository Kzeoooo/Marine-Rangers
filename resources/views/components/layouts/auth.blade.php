@props([
    'title' => 'Autentikasi - Marine Rangers',
    'activeTab' => 'login',
    'totalLaporan' => null,
    'totalSelesai' => null,
])
@include('layouts.auth', [
    'title' => $title,
    'activeTab' => $activeTab,
    'totalLaporan' => $totalLaporan,
    'totalSelesai' => $totalSelesai,
    'slot' => $slot,
])
