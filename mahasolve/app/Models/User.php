<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'email',
        'password',
        'no_hp',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relasi 1-1: user provider punya satu baris provider
    public function provider()
    {
        return $this->hasOne(Provider::class, 'id_user', 'id_user');
    }

    // Relasi 1-n: user mahasiswa punya banyak request_layanan
    public function requestLayanan()
    {
        return $this->hasMany(RequestLayanan::class, 'id_user', 'id_user');
    }

    public function getActiveRole(): string
    {
        return session('active_role', $this->role ?? 'mahasiswa');
    }

    public function isMahasiswa(): bool
    {
        return $this->getActiveRole() === 'mahasiswa';
    }

    public function isProvider(): bool
    {
        return $this->getActiveRole() === 'provider';
    }

    // Helper: Ambil atau buat otomatis entri Provider untuk user ini
    public function getOrCreateProvider(): Provider
    {
        return $this->provider ?? Provider::create([
            'id_user' => $this->id_user,
            'rating' => 5.0,
            'detail_provider' => 'Mitra Jasa Terverifikasi Mahasolve',
            'status_verifikasi' => 'terverifikasi',
            'nomor_ktm' => 'KTM-UNIKOM-' . str_pad((string)$this->id_user, 5, '0', STR_PAD_LEFT),
            'nomor_sim' => 'SIM-C-BANDUNG',
        ]);
    }

    // Alias supaya Auth::user()->name tetap jalan (beberapa view bawaan
    // Laravel Breeze memanggil ->name, sedangkan kolom kita bernama 'username')
    public function getNameAttribute(): string
    {
        return $this->username;
    }
}
