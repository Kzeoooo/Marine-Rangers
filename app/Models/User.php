<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    // Konstanta Role Pengguna (bukan angka sihir)
    public const ROLE_MASYARAKAT = 'masyarakat';
    public const ROLE_PEMANTAU = 'pemantau';
    public const ROLE_ADMIN = 'admin';

    public const ROLES = [
        self::ROLE_MASYARAKAT,
        self::ROLE_PEMANTAU,
        self::ROLE_ADMIN,
    ];

    /**
     * Default model attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_MASYARAKAT,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'organisasi',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Role Helper Methods
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPemantau(): bool
    {
        return $this->role === self::ROLE_PEMANTAU;
    }

    public function isMasyarakat(): bool
    {
        return $this->role === self::ROLE_MASYARAKAT;
    }

    // Relasi Eloquent
    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'id_user', 'id_user');
    }

    public function pembersihan(): HasMany
    {
        return $this->hasMany(Pembersihan::class, 'id_user', 'id_user');
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'id_user', 'id_user');
    }

    public function dashboard(): HasMany
    {
        return $this->hasMany(Dashboard::class, 'id_user', 'id_user');
    }
}
