<?php

namespace App\Helpers;

use App\Models\LogAktivitas;
use Carbon\Carbon;

class LogAktivitasHelper
{
    /**
     * Catat aktivitas pengguna ke tabel log_aktivitas.
     *
     * @param int|string $idUser ID pengguna (primary key id_user)
     * @param string $aktivitas Deskripsi aktivitas
     * @return LogAktivitas
     */
    public static function catat($idUser, string $aktivitas): LogAktivitas
    {
        return LogAktivitas::create([
            'id_user' => $idUser,
            'aktivitas' => $aktivitas,
            'waktu' => Carbon::now('Asia/Jakarta'),
        ]);
    }
}