<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\Negosiasi;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Provider;
use App\Models\RatingReview;
use App\Models\RequestLayanan;
use App\Models\TrackingPesanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ExemplarBusinessFlowSeeder extends Seeder
{
    public function run(): void
    {
        // Clean up previous test entries for clean 2-account model
        DB::statement('TRUNCATE TABLE rating_review, pembayaran, tracking_pesanan, detail_pekerjaan, pesanan, negosiasi, request_layanan, layanan, provider, "user" RESTART IDENTITY CASCADE;');

        // ==========================================
        // 1. DUA AKUN PERCONTOHAN UAT MAHASOLVE
        // ==========================================

        // 1. Akun Mahasiswa (Klien Utama): Budi Santoso
        $budi = User::create([
            'email' => 'budi.santoso@mahasiswa.unikom.ac.id',
            'username' => 'budi_santoso',
            'password' => Hash::make('password123'),
            'no_hp' => '081234567890',
            'role' => 'mahasiswa',
        ]);

        // 2. Akun Provider (Mitra Utama): Dewi Lestari
        $dewiUser = User::create([
            'email' => 'dewi.lestari@mahasiswa.unikom.ac.id',
            'username' => 'dewi_lestari',
            'password' => Hash::make('password123'),
            'no_hp' => '081987654321',
            'role' => 'provider',
        ]);

        // ==========================================
        // 2. PROFIL MITRA PROVIDER TERVERIFIKASI
        // ==========================================

        $providerDewi = Provider::create([
            'id_user' => $dewiUser->id_user,
            'rating' => 5.0,
            'detail_provider' => 'Mitra Penyedia Jasa Akademik, Bimbingan RPL, Print Skripsi & Titip Beli Area Unikom',
            'status_verifikasi' => 'terverifikasi',
            'nomor_ktm' => 'KTM-UNIKOM-102948',
            'nomor_sim' => 'SIM-C-293849102',
        ]);

        // ==========================================
        // 3. KATALOG LAYANAN LENGKAP (4 KATEGORI)
        // ==========================================

        $layananBimbingan = Layanan::create([
            'id_provider' => $providerDewi->id_provider,
            'nama_layanan' => 'Bimbingan & Tutor Tugas RPL Unikom',
            'kategori' => 'Bimbingan',
            'deskripsi' => 'Bimbingan praktikum pemrograman Laravel 11, Clean Architecture, dan persiapan sidang komprehensif.',
            'harga' => 50000,
            'estimasi_pengerjaan' => '1 Hari',
        ]);

        $layananPrint = Layanan::create([
            'id_provider' => $providerDewi->id_provider,
            'nama_layanan' => 'Jasa Print & Jilid Hardcover Skripsi Unikom',
            'kategori' => 'Print & Fotokopi',
            'deskripsi' => 'Cetak tugas akhir/skripsi warna HVS 80gr dan jilid hardcover pita emas sesuai standar Unikom.',
            'harga' => 35000,
            'estimasi_pengerjaan' => '3 Jam',
        ]);

        $layananAntar = Layanan::create([
            'id_provider' => $providerDewi->id_provider,
            'nama_layanan' => 'Antar Jemput Motor Kampus Dipatiukur',
            'kategori' => 'Antar Jemput',
            'deskripsi' => 'Antar jemput cepat & aman helm bersih area Dipatiukur, Dago, & Sekitar Kampus Unikom.',
            'harga' => 10000,
            'estimasi_pengerjaan' => '15 Menit',
        ]);

        $layananTitip = Layanan::create([
            'id_provider' => $providerDewi->id_provider,
            'nama_layanan' => 'Titip Beli Makanan Kantin & Minimarket',
            'kategori' => 'Titip Beli',
            'deskripsi' => 'Layanan titip beli makanan kantin kampus, alat tulis perkuliahan, dan kebutuhan kosan.',
            'harga' => 15000,
            'estimasi_pengerjaan' => '30 Menit',
        ]);

        // ==========================================
        // 4. SKENARIO BUSINESS FLOW 1: PESANAN SELESAI + ULASAN (Budi & Dewi)
        // ==========================================

        $req1 = RequestLayanan::create([
            'id_user' => $budi->id_user,
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Tutor Bimbingan Task Clean Architecture Laravel 11',
            'harga_awal' => 50000,
            'status_request' => 'selesai',
        ]);

        $nego1 = Negosiasi::create([
            'id_request' => $req1->id_request,
            'id_provider' => $providerDewi->id_provider,
            'harga_tawaran' => 45000,
            'dibuat_oleh' => 'provider',
            'detail_negosiasi' => 'Bisa dibimbing langsung di Perpustakaan Unikom Lantai 3',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan1 = Pesanan::create([
            'id_negosiasi' => $nego1->id_negosiasi,
            'harga_final' => 45000,
            'status_pesanan' => 'selesai',
        ]);

        TrackingPesanan::create([
            'id_pesanan' => $pesanan1->id_pesanan,
            'status_pengerjaan' => 'Sesi bimbingan tuntas dilaksanakan dan disetujui mahasiswa.',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan1->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom',
            'total_bayar' => 45000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        RatingReview::create([
            'id_pesanan' => $pesanan1->id_pesanan,
            'rate' => 5,
            'review' => 'Penjelasan sangat jelas, sabar, dan membantu tugas akhir saya selesai dengan sangat rapi!',
        ]);

        // ==========================================
        // 5. SKENARIO BUSINESS FLOW 2: PESANAN SEDANG DIPROSES
        // ==========================================

        $reqPrint = RequestLayanan::create([
            'id_user' => $budi->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Print 50 halaman HVS 80gr dan jilid pita emas',
            'harga_awal' => 35000,
            'status_request' => 'diproses',
        ]);

        $negoPrint = Negosiasi::create([
            'id_request' => $reqPrint->id_request,
            'id_provider' => $providerDewi->id_provider,
            'harga_tawaran' => 35000,
            'dibuat_oleh' => 'provider',
            'detail_negosiasi' => 'Siap diprint HVS 80gr jilid pita emas',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan2 = Pesanan::create([
            'id_negosiasi' => $negoPrint->id_negosiasi,
            'harga_final' => 35000,
            'status_pesanan' => 'dikerjakan',
        ]);

        TrackingPesanan::create([
            'id_pesanan' => $pesanan2->id_pesanan,
            'status_pengerjaan' => 'Dokumen sedang dicetak dan dikeringkan jilidnya.',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan2->id_pesanan,
            'metode_pembayaran' => 'BCA Virtual Account',
            'total_bayar' => 35000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        // ==========================================
        // 6. SKENARIO BUSINESS FLOW 3: NEGOSIASI AKTIF
        // ==========================================

        $req2 = RequestLayanan::create([
            'id_user' => $budi->id_user,
            'kategori' => 'Titip Beli',
            'detail_kebutuhan' => 'Titip beli 2 porsi Ayam Geprek Kantin Parkiran Depan Unikom',
            'harga_awal' => 30000,
            'status_request' => 'open',
        ]);

        Negosiasi::create([
            'id_request' => $req2->id_request,
            'id_provider' => $providerDewi->id_provider,
            'harga_tawaran' => 28000,
            'dibuat_oleh' => 'provider',
            'detail_negosiasi' => 'Bisa dibelikan sekaligus antar ke Ruang 4.02 Kampus Unikom',
            'status_negosiasi' => 'pending',
        ]);
    }
}
