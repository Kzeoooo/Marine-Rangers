<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dashboard extends Model
{
    use HasFactory;

    protected $table = 'dashboard';
    protected $primaryKey = 'id_dashboard';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'periode_mulai',
        'periode_selesai',
        'jumlah_laporan',
        'tren_laporan',
        'wilayah_prioritas',
        'dibuat_pada',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
            'jumlah_laporan' => 'integer',
            'dibuat_pada' => 'datetime',
        ];
    }

    // Relasi Eloquent
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

