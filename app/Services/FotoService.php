<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class FotoService
{
    /**
     * Batas dimensi sisi terpanjang foto (maks 1600 px).
     */
    public const MAX_DIMENSION = 1600;

    /**
     * Kualitas kompresi output JPEG (~80%).
     */
    public const JPEG_QUALITY = 80;

    /**
     * Kompres dan simpan foto menggunakan ekstensi PHP GD.
     * Resize sisi terpanjang maks 1600 px dan simpan ulang dengan kualitas JPEG ~80.
     *
     * @param  UploadedFile  $file   File foto yang diunggah
     * @param  string        $folder Folder tujuan pada disk public (misal: 'laporan' atau 'pembersihan')
     * @return string Path relatif file yang disimpan (misal: 'laporan/xyz.jpg')
     */
    public static function kompresDanSimpan(UploadedFile $file, string $folder = 'laporan'): string
    {
        $realPath = $file->getRealPath();
        $mime = $file->getMimeType();

        // 1. Muat resource gambar sumber menggunakan GD sesuai format
        $source = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($realPath),
            'image/png' => @imagecreatefrompng($realPath),
            'image/webp' => @imagecreatefromwebp($realPath),
            default => @imagecreatefromstring((string) file_get_contents($realPath)),
        };

        if (! $source) {
            throw new RuntimeException('Gagal memproses file gambar dengan ekstensi GD.');
        }

        // 2. Koreksi rotasi EXIF untuk foto kamera HP (jika ada data orientasi)
        if (function_exists('exif_read_data') && in_array($mime, ['image/jpeg', 'image/jpg'])) {
            try {
                $exif = @exif_read_data($realPath);
                if (! empty($exif['Orientation'])) {
                    $source = match ($exif['Orientation']) {
                        3 => imagerotate($source, 180, 0),
                        6 => imagerotate($source, -90, 0),
                        8 => imagerotate($source, 90, 0),
                        default => $source,
                    };
                }
            } catch (\Throwable $e) {
                // Abaikan jika EXIF tidak terbaca
            }
        }

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        // 3. Hitung dimensi baru: sisi terpanjang maks 1600 px proporsional
        if ($origWidth > self::MAX_DIMENSION || $origHeight > self::MAX_DIMENSION) {
            if ($origWidth >= $origHeight) {
                $newWidth = self::MAX_DIMENSION;
                $newHeight = (int) round(($origHeight / $origWidth) * self::MAX_DIMENSION);
            } else {
                $newHeight = self::MAX_DIMENSION;
                $newWidth = (int) round(($origWidth / $origHeight) * self::MAX_DIMENSION);
            }
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        // 4. Siapkan canvas baru dengan latar belakang putih bersih
        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        // 5. Resample gambar ke ukuran baru
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // 6. Generate nama berkas unik dan simpan ke storage disk 'public'
        $filename = Str::random(40) . '.jpg';
        $relativePath = trim($folder, '/') . '/' . $filename;
        $fullPath = Storage::disk('public')->path($relativePath);

        File::ensureDirectoryExists(dirname($fullPath));

        // Simpan sebagai JPEG kualitas 80
        imagejpeg($canvas, $fullPath, self::JPEG_QUALITY);

        // Bersihkan memori GD
        imagedestroy($source);
        imagedestroy($canvas);

        return $relativePath;
    }
}
