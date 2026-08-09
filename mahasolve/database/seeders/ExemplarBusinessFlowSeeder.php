<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\Negosiasi;
use App\Models\Pembayaran;
use App\Models\PesanNegosiasi;
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
        // Bersihkan seluruh data dummy sebelumnya untuk memastikan HANYA DUA AKUN yang ada
        DB::statement('TRUNCATE TABLE penarikan_saldo, pesan_negosiasi, rating_review, pembayaran, tracking_pesanan, detail_pekerjaan, pesanan, negosiasi, request_layanan, layanan, provider, "user" RESTART IDENTITY CASCADE;');

        // ==========================================
        // 1. DUA AKUN UTAMA APLIKASI MAHASOLVE
        // ==========================================

        // Akun 1: Ragil Maulana (ragil.10124707)
        $ragil = User::create([
            'email' => 'ragil.10124707@mahasiswa.unikom.ac.id',
            'username' => 'aphroditekenny',
            'password' => Hash::make('password'),
            'no_hp' => '081247071012',
            'role' => 'mahasiswa',
        ]);

        // Akun 2: Rizki Maulana (rizki.maulana)
        $rizki = User::create([
            'email' => 'rizki.maulana@gmail.com',
            'username' => 'rizki_maulana',
            'password' => Hash::make('password'),
            'no_hp' => '081234567890',
            'role' => 'provider',
        ]);

        // ==========================================
        // 2. PROFIL MITRA PROVIDER TERVERIFIKASI
        // ==========================================

        // Profil Provider Rizki Maulana
        $providerRizki = Provider::create([
            'id_user' => $rizki->id_user,
            'rating' => 5.0,
            'detail_provider' => 'Mitra Penyedia Jasa Utama Mahasolve — Bimbingan Academic Coding, Print Skripsi, Antar Jemput & Titip Beli Unikom',
            'status_verifikasi' => 'terverifikasi',
            'nomor_ktm' => 'KTM-UNIKOM-10124000',
            'nomor_sim' => 'SIM-C-10124000',
        ]);

        // Profil Provider Ragil Maulana (agar ragil juga bisa bertindak sebagai provider)
        $providerRagil = Provider::create([
            'id_user' => $ragil->id_user,
            'rating' => 4.9,
            'detail_provider' => 'Mitra Penyedia Jasa Software Engineering, Modern Web Dev & Slide Presentasi Sidang Unikom',
            'status_verifikasi' => 'terverifikasi',
            'nomor_ktm' => 'KTM-UNIKOM-10124707',
            'nomor_sim' => 'SIM-C-10124707',
        ]);

        // ==========================================
        // 3. KATALOG LAYANAN (DITAWARKAN OLEH MITRA)
        // ==========================================

        // Layanan oleh Provider Rizki
        $layananBimbingan = Layanan::create([
            'id_provider' => $providerRizki->id_provider,
            'nama_layanan' => 'Bimbingan & Tutor Tugas RPL Unikom',
            'kategori' => 'Bimbingan',
            'deskripsi' => 'Bimbingan praktikum pemrograman Laravel 11, Clean Architecture, dan persiapan sidang komprehensif.',
            'harga' => 50000,
            'estimasi_pengerjaan' => '1 Hari',
        ]);

        $layananPrint = Layanan::create([
            'id_provider' => $providerRizki->id_provider,
            'nama_layanan' => 'Jasa Print & Jilid Hardcover Skripsi Unikom',
            'kategori' => 'Print & Fotokopi',
            'deskripsi' => 'Cetak tugas akhir/skripsi warna HVS 80gr dan jilid hardcover pita emas sesuai standar Unikom.',
            'harga' => 35000,
            'estimasi_pengerjaan' => '3 Jam',
        ]);

        $layananAntar = Layanan::create([
            'id_provider' => $providerRizki->id_provider,
            'nama_layanan' => 'Antar Jemput Motor Kampus Dipatiukur',
            'kategori' => 'Antar Jemput',
            'deskripsi' => 'Antar jemput cepat & aman helm bersih area Dipatiukur, Dago, & Sekitar Kampus Unikom.',
            'harga' => 10000,
            'estimasi_pengerjaan' => '15 Menit',
        ]);

        $layananTitip = Layanan::create([
            'id_provider' => $providerRizki->id_provider,
            'nama_layanan' => 'Titip Beli Makanan Kantin & Minimarket',
            'kategori' => 'Titip Beli',
            'deskripsi' => 'Layanan titip beli makanan kantin kampus, alat tulis perkuliahan, dan kebutuhan kosan.',
            'harga' => 15000,
            'estimasi_pengerjaan' => '30 Menit',
        ]);

        // Layanan oleh Provider Ragil
        $layananCoding = Layanan::create([
            'id_provider' => $providerRagil->id_provider,
            'nama_layanan' => 'Pengembangan Web Application Laravel & Next.js',
            'kategori' => 'Bimbingan',
            'deskripsi' => 'Jasa pembuatan & konsultasi arsitektur web modern fullstack untuk tugas akhir atau riset RPL.',
            'harga' => 75000,
            'estimasi_pengerjaan' => '2 Hari',
        ]);

        $layananPPT = Layanan::create([
            'id_provider' => $providerRagil->id_provider,
            'nama_layanan' => 'Desain Slide Presentasi Sidang Komprehensif PPT',
            'kategori' => 'Bimbingan',
            'deskripsi' => 'Desain visual slide presentasi sidang skripsi profesional & animasi modern siap tampil.',
            'harga' => 30000,
            'estimasi_pengerjaan' => '1 Hari',
        ]);

        // ==========================================
        // 4. SKENARIO BUSINESS FLOW 1: PESANAN SELESAI + ULASAN (Ragil = Klien, Rizki = Provider)
        // ==========================================

        $req1 = RequestLayanan::create([
            'id_user' => $ragil->id_user,
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Tutor Bimbingan Task Clean Architecture Laravel 11',
            'harga_awal' => 50000,
            'status_request' => 'selesai',
        ]);

        $nego1 = Negosiasi::create([
            'id_request' => $req1->id_request,
            'id_provider' => $providerRizki->id_provider,
            'harga_tawaran' => 45000,
            'dibuat_oleh' => 'provider',
            'detail_negosiasi' => 'Bisa dibimbing langsung di Perpustakaan Unikom Lantai 3',
            'status_negosiasi' => 'disepakati',
        ]);

        PesanNegosiasi::create([
            'id_negosiasi' => $nego1->id_negosiasi,
            'id_pengirim' => $ragil->id_user,
            'peran_pengirim' => 'mahasiswa',
            'pesan' => 'Halo Mas Rizki, mau minta bimbingan Clean Architecture Laravel 11 dong.',
            'harga_tawaran' => 50000,
        ]);

        PesanNegosiasi::create([
            'id_negosiasi' => $nego1->id_negosiasi,
            'id_pengirim' => $rizki->id_user,
            'peran_pengirim' => 'provider',
            'pesan' => 'Bisa dibimbing langsung di Perpustakaan Unikom Lantai 3. Saya kasih penawaran Rp45.000 ya.',
            'harga_tawaran' => 45000,
        ]);

        $pesanan1 = Pesanan::create([
            'id_negosiasi' => $nego1->id_negosiasi,
            'harga_final' => 45000,
            'status_pesanan' => 'selesai',
        ]);

        TrackingPesanan::create([
            'id_pesanan' => $pesanan1->id_pesanan,
            'status_pengerjaan' => 'Sesi bimbingan tuntas dilaksanakan di Perpustakaan Unikom L-3 dan disetujui.',
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
            'review' => 'Penjelasan Mas Rizki sangat jelas, sabar, dan membantu tugas akhir saya rilis tepat waktu!',
        ]);

        $providerRizki->refreshRating();

        // ==========================================
        // 5. SKENARIO BUSINESS FLOW 2: PESANAN SEDANG DIPROSES (Ragil = Klien, Rizki = Provider)
        // ==========================================

        $reqPrint = RequestLayanan::create([
            'id_user' => $ragil->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Print 50 halaman HVS 80gr dan jilid pita emas',
            'harga_awal' => 35000,
            'status_request' => 'diproses',
        ]);

        $negoPrint = Negosiasi::create([
            'id_request' => $reqPrint->id_request,
            'id_provider' => $providerRizki->id_provider,
            'harga_tawaran' => 35000,
            'dibuat_oleh' => 'provider',
            'detail_negosiasi' => 'Siap diprint HVS 80gr jilid pita emas',
            'status_negosiasi' => 'disepakati',
        ]);

        PesanNegosiasi::create([
            'id_negosiasi' => $negoPrint->id_negosiasi,
            'id_pengirim' => $ragil->id_user,
            'peran_pengirim' => 'mahasiswa',
            'pesan' => 'Mas Rizki, tolong printkan berkas skripsi saya 50 lembar ya.',
            'harga_tawaran' => 35000,
        ]);

        PesanNegosiasi::create([
            'id_negosiasi' => $negoPrint->id_negosiasi,
            'id_pengirim' => $rizki->id_user,
            'peran_pengirim' => 'provider',
            'pesan' => 'Siap diprint HVS 80gr jilid pita emas. Penawaran disetujui.',
            'harga_tawaran' => 35000,
        ]);

        $pesanan2 = Pesanan::create([
            'id_negosiasi' => $negoPrint->id_negosiasi,
            'harga_final' => 35000,
            'status_pesanan' => 'dikerjakan',
        ]);

        TrackingPesanan::create([
            'id_pesanan' => $pesanan2->id_pesanan,
            'status_pengerjaan' => 'Dokumen sedang dicetak dan dikeringkan jilid pita emas di Percetakan Kampus.',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan2->id_pesanan,
            'metode_pembayaran' => 'BCA Virtual Account',
            'total_bayar' => 35000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        // ==========================================
        // 6. SKENARIO BUSINESS FLOW 3: NEGOSIASI AKTIF (Ragil = Klien, Rizki = Provider)
        // ==========================================

        $req2 = RequestLayanan::create([
            'id_user' => $ragil->id_user,
            'kategori' => 'Titip Beli',
            'detail_kebutuhan' => 'Titip beli 2 porsi Ayam Geprek Kantin Parkiran Depan Unikom',
            'harga_awal' => 30000,
            'status_request' => 'open',
        ]);

        $nego2 = Negosiasi::create([
            'id_request' => $req2->id_request,
            'id_provider' => $providerRizki->id_provider,
            'harga_tawaran' => 28000,
            'dibuat_oleh' => 'provider',
            'detail_negosiasi' => 'Bisa dibelikan sekaligus antar ke Ruang 4.02 Kampus Unikom',
            'status_negosiasi' => 'pending',
        ]);

        PesanNegosiasi::create([
            'id_negosiasi' => $nego2->id_negosiasi,
            'id_pengirim' => $ragil->id_user,
            'peran_pengirim' => 'mahasiswa',
            'pesan' => 'Mas Rizki, bisa tolong belikan Ayam Geprek 2 porsi?',
            'harga_tawaran' => 30000,
        ]);

        PesanNegosiasi::create([
            'id_negosiasi' => $nego2->id_negosiasi,
            'id_pengirim' => $rizki->id_user,
            'peran_pengirim' => 'provider',
            'pesan' => 'Bisa dibelikan sekaligus antar ke Ruang 4.02 Kampus Unikom. Saya tawar Rp28.000 ya.',
            'harga_tawaran' => 28000,
        ]);

        // ==========================================
        // 7. SKENARIO BUSINESS FLOW 4: PESANAN REVERSE (Rizki = Klien, Ragil = Provider)
        // ==========================================

        $reqPPT = RequestLayanan::create([
            'id_user' => $rizki->id_user,
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Desain Slide Presentasi PPT Sidang Komprehensif Skripsi',
            'harga_awal' => 30000,
            'status_request' => 'selesai',
        ]);

        $negoPPT = Negosiasi::create([
            'id_request' => $reqPPT->id_request,
            'id_provider' => $providerRagil->id_provider,
            'harga_tawaran' => 30000,
            'dibuat_oleh' => 'provider',
            'detail_negosiasi' => 'Desain PPT animasi modern siap dalam 1 hari',
            'status_negosiasi' => 'disepakati',
        ]);

        PesanNegosiasi::create([
            'id_negosiasi' => $negoPPT->id_negosiasi,
            'id_pengirim' => $rizki->id_user,
            'peran_pengirim' => 'mahasiswa',
            'pesan' => 'Mas Ragil, minta bantuan bikin slide presentasi PPT sidang skripsi ya.',
            'harga_tawaran' => 30000,
        ]);

        PesanNegosiasi::create([
            'id_negosiasi' => $negoPPT->id_negosiasi,
            'id_pengirim' => $ragil->id_user,
            'peran_pengirim' => 'provider',
            'pesan' => 'Siap Mas Rizki, slide akan didesain animasi modern profesional.',
            'harga_tawaran' => 30000,
        ]);

        $pesanan3 = Pesanan::create([
            'id_negosiasi' => $negoPPT->id_negosiasi,
            'harga_final' => 30000,
            'status_pesanan' => 'selesai',
        ]);

        TrackingPesanan::create([
            'id_pesanan' => $pesanan3->id_pesanan,
            'status_pengerjaan' => 'File PPT Sidang komprehensif telah diserahkan & direvisi tuntas.',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan3->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom',
            'total_bayar' => 30000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        RatingReview::create([
            'id_pesanan' => $pesanan3->id_pesanan,
            'rate' => 5,
            'review' => 'Slide PPT buatan Ragil sangat profesional, visualnya tajam & modern!',
        ]);

        $providerRagil->refreshRating();
    }
}
