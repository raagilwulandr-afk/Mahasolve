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
        'password' => 'hashed',
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

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    // Alias supaya Auth::user()->name tetap jalan (beberapa view bawaan
    // Laravel Breeze memanggil ->name, sedangkan kolom kita bernama 'username')
    public function getNameAttribute(): string
    {
        return $this->username;
    }
}
