<?php

namespace App\Models;

use Database\Factories\PembersihanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembersihan extends Model
{
    /** @use HasFactory<PembersihanFactory> */
    use HasFactory;

    protected $table = 'pembersihan';
    protected $primaryKey = 'id_pembersihan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_laporan',
        'id_user',
        'tanggal',
        'deskripsi_hasil',
        'foto_hasil',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    // Relasi Eloquent
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class, 'id_laporan', 'id_laporan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

