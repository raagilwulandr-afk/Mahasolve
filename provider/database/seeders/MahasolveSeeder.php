<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MahasolveSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user')->insert([
            [
                'username' => 'Raihan',
                'email' => 'provider@mahasolve.com',
                'password' => Hash::make('password'),
                'no_hp' => '081234567890',
                'role' => 'provider',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Mahasiswa',
                'email' => 'mahasiswa@mahasolve.com',
                'password' => Hash::make('password'),
                'no_hp' => '081111111111',
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
