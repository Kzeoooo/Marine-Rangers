<?php

namespace App\Models;

use Database\Factories\LaporanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laporan extends Model
{
    /** @use HasFactory<LaporanFactory> */
    use HasFactory;

    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';

    // Konstanta Status Laporan (bukan angka sihir)
    public const STATUS_BARU = 'baru';
    public const STATUS_DIPANTAU = 'dipantau';
    public const STATUS_DITINDAKLANJUTI = 'ditindaklanjuti';
    public const STATUS_SELESAI = 'selesai';

    public const STATUSES = [
        self::STATUS_BARU,
        self::STATUS_DIPANTAU,
        self::STATUS_DITINDAKLANJUTI,
        self::STATUS_SELESAI,
    ];

    /**
     * Default model attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_BARU,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'id_kategori',
        'foto',
        'latitude',
        'longitude',
        'wilayah',
        'deskripsi',
        'waktu_laporan',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'waktu_laporan' => 'datetime',
        ];
    }

    // Status Helper Methods
    public function isBaru(): bool
    {
        return $this->status === self::STATUS_BARU;
    }

    public function isDipantau(): bool
    {
        return $this->status === self::STATUS_DIPANTAU;
    }

    public function isDitindaklanjuti(): bool
    {
        return $this->status === self::STATUS_DITINDAKLANJUTI;
    }

    public function isSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    // Relasi Eloquent
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function pembersihan(): HasMany
    {
        return $this->hasMany(Pembersihan::class, 'id_laporan', 'id_laporan');
    }
}
