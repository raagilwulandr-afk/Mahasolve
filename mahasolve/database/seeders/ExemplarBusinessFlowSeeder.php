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
use Illuminate\Support\Facades\Hash;

class ExemplarBusinessFlowSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. ANGGOTA USERS & PERSONA UAT MAHASOLVE
        // ==========================================

        // Mahasiswa Klien 1 (Budi Santoso - Teknik Informatika)
        $budi = User::updateOrCreate(
            ['email' => 'budi.santoso@mahasiswa.unikom.ac.id'],
            [
                'username' => 'budi_santoso',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567890',
                'role' => 'mahasiswa',
            ]
        );

        // Mahasiswa Klien 2 (Siti Nurhaliza - Desain Komunikasi Visual)
        $siti = User::updateOrCreate(
            ['email' => 'siti.nurhaliza@mahasiswa.unikom.ac.id'],
            [
                'username' => 'siti_nurhaliza',
                'password' => Hash::make('password123'),
                'no_hp' => '081398765432',
                'role' => 'mahasiswa',
            ]
        );

        // Mahasiswa Provider 1 (Dewi Lestari - Akademik & Print)
        $dewiUser = User::updateOrCreate(
            ['email' => 'dewi.lestari@mahasiswa.unikom.ac.id'],
            [
                'username' => 'dewi_lestari',
                'password' => Hash::make('password123'),
                'no_hp' => '081987654321',
                'role' => 'provider',
            ]
        );

        // Mahasiswa Provider 2 (Rizky Maulana - Mobilitas & Titip Beli)
        $rizkyUser = User::updateOrCreate(
            ['email' => 'rizky.maulana@mahasiswa.unikom.ac.id'],
            [
                'username' => 'rizky_antar',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567804',
                'role' => 'provider',
            ]
        );

        // ==========================================
        // 2. PROFIL MITRA PROVIDER TERVERIFIKASI
        // ==========================================

        $providerDewi = Provider::updateOrCreate(
            ['id_user' => $dewiUser->id_user],
            [
                'rating' => 5.0,
                'detail_provider' => 'Mitra Penyedia Jasa Akademik, Tutoring RPL & Cetak Tugas Terverifikasi Unikom',
            ]
        );

        $providerRizky = Provider::updateOrCreate(
            ['id_user' => $rizkyUser->id_user],
            [
                'rating' => 4.9,
                'detail_provider' => 'Layanan Antar Jemput Motor Kampus & Titip Beli Cepat Area Dipatiukur',
            ]
        );

        // ==========================================
        // 3. KATALOG LAYANAN RESMI (4 KATEGORI UTAMA)
        // ==========================================

        // Kategori: Bimbingan
        Layanan::updateOrCreate(
            ['id_provider' => $providerDewi->id_provider, 'nama_layanan' => 'Bimbingan & Tutor Tugas RPL Unikom'],
            [
                'kategori' => 'Bimbingan',
                'deskripsi' => 'Bimbingan praktikum pemrograman Laravel 11, Clean Architecture, dan persiapan komprehensif.',
                'harga' => 50000,
                'estimasi_pengerjaan' => '1 Hari',
            ]
        );

        // Kategori: Print & Fotokopi
        Layanan::updateOrCreate(
            ['id_provider' => $providerDewi->id_provider, 'nama_layanan' => 'Cetak Skripsi & Jilid Hardcover Unikom'],
            [
                'kategori' => 'Print & Fotokopi',
                'deskripsi' => 'Print warna kualitas tinggi, jilid mika/hardcover pita emas sesuai standar perpustakaan Unikom.',
                'harga' => 35000,
                'estimasi_pengerjaan' => '3 Jam',
            ]
        );

        // Kategori: Antar Jemput
        Layanan::updateOrCreate(
            ['id_provider' => $providerRizky->id_provider, 'nama_layanan' => 'Antar Jemput Motor Kampus Unikom'],
            [
                'kategori' => 'Antar Jemput',
                'deskripsi' => 'Antar jemput cepat & aman helm bersih area Dipatiukur, Dago, & Sekitar Kampus Unikom.',
                'harga' => 10000,
                'estimasi_pengerjaan' => '15 Menit',
            ]
        );

        // Kategori: Titip Beli
        Layanan::updateOrCreate(
            ['id_provider' => $providerRizky->id_provider, 'nama_layanan' => 'Titip Beli Makanan Kantin & Minimarket'],
            [
                'kategori' => 'Titip Beli',
                'deskripsi' => 'Layanan titip beli makanan kantin kampus, alat tulis perkuliahan, dan kebutuhan kosan.',
                'harga' => 15000,
                'estimasi_pengerjaan' => '30 Menit',
            ]
        );

        // ==========================================
        // 4. SKENARIO UAT BUSINESS LIFECYCLE 1:
        // Completed Order + 5-Star Review (Budi & Dewi - Bimbingan)
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
            'harga_tawaran' => 50000,
            'detail_negosiasi' => 'Disepakati harga bimbingan Rp50.000 dengan jadwal modul hari Sabtu.',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan1 = Pesanan::create([
            'id_negosiasi' => $nego1->id_negosiasi,
            'harga_final' => 50000,
            'tanggal_pesanan' => now()->subDays(2),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan1->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay',
            'total_bayar' => 50000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        TrackingPesanan::create([
            'id_pesanan' => $pesanan1->id_pesanan,
            'status_pengerjaan' => 'Pekerjaan bimbingan telah selesai 100% dan berkas modul diserahterimakan.',
        ]);

        RatingReview::create([
            'id_pesanan' => $pesanan1->id_pesanan,
            'rate' => 5,
            'review' => 'Sangat merekomendasikan Mbak Dewi! Penjelasan bimbingan sangat mudah dipahami dan profesional.',
        ]);

        // ==========================================
        // 5. SKENARIO UAT BUSINESS LIFECYCLE 2:
        // Active Ongoing Order in Progress (Budi & Rizky - Antar Jemput)
        // ==========================================

        $req2 = RequestLayanan::create([
            'id_user' => $budi->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput dari Kosan Dipatiukur ke Gedung Utama Unikom',
            'harga_awal' => 10000,
            'status_request' => 'diproses',
        ]);

        $nego2 = Negosiasi::create([
            'id_request' => $req2->id_request,
            'id_provider' => $providerRizky->id_provider,
            'harga_tawaran' => 10000,
            'detail_negosiasi' => 'Siap jemput jam 08:00 WIB depan gerbang kosan.',
            'dibuat_oleh' => 'provider',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan2 = Pesanan::create([
            'id_negosiasi' => $nego2->id_negosiasi,
            'harga_final' => 10000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan2->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay',
            'total_bayar' => 10000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        TrackingPesanan::create([
            'id_pesanan' => $pesanan2->id_pesanan,
            'status_pengerjaan' => 'Mitra sedang dalam perjalanan menuju lokasi penjemputan Gedung Utama Unikom.',
        ]);

        // ==========================================
        // 6. SKENARIO UAT BUSINESS LIFECYCLE 3:
        // Negotiation Phase - Counter Offer (Siti & Dewi - Print Skripsi)
        // ==========================================

        $req3 = RequestLayanan::create([
            'id_user' => $siti->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Cetak 100 Halaman Skripsi + Jilid Hardcover Pita Emas Unikom',
            'harga_awal' => 30000,
            'status_request' => 'diproses',
        ]);

        Negosiasi::create([
            'id_request' => $req3->id_request,
            'id_provider' => $providerDewi->id_provider,
            'harga_tawaran' => 35000,
            'detail_negosiasi' => 'Bisa tambah biaya jilid pita emas standar perpustakaan Rp5.000?',
            'dibuat_oleh' => 'provider',
            'status_negosiasi' => 'ditawar_ulang',
        ]);

        // ==========================================
        // 7. SKENARIO UAT BUSINESS LIFECYCLE 4:
        // Custom Request Open Waiting for Bids (Siti - Titip Beli)
        // ==========================================

        RequestLayanan::create([
            'id_user' => $siti->id_user,
            'kategori' => 'Titip Beli',
            'detail_kebutuhan' => 'Titip Beli Nasi Ayam Geprek Kantin Parkiran Depan Unikom & Es Teh Jumbo',
            'harga_awal' => 20000,
            'status_request' => 'open',
        ]);

        // ==========================================
        // 8. SKENARIO UAT BUSINESS LIFECYCLE 5:
        // History Completed Transport Order + 5-Star Review (Siti & Rizky)
        // ==========================================

        $req5 = RequestLayanan::create([
            'id_user' => $siti->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Malam Perpustakaan Unikom ke Simpang Dago',
            'harga_awal' => 12000,
            'status_request' => 'selesai',
        ]);

        $nego5 = Negosiasi::create([
            'id_request' => $req5->id_request,
            'id_provider' => $providerRizky->id_provider,
            'harga_tawaran' => 12000,
            'detail_negosiasi' => 'Siap antar selamat sampai tujuan.',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan5 = Pesanan::create([
            'id_negosiasi' => $nego5->id_negosiasi,
            'harga_final' => 12000,
            'tanggal_pesanan' => now()->subDays(1),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan5->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay',
            'total_bayar' => 12000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        TrackingPesanan::create([
            'id_pesanan' => $pesanan5->id_pesanan,
            'status_pengerjaan' => 'Penumpang telah diantar sampai tujuan dengan selamat.',
        ]);

        RatingReview::create([
            'id_pesanan' => $pesanan5->id_pesanan,
            'rate' => 5,
            'review' => 'Kang Rizky sangat ramah, berkendara aman, dan selalu tepat waktu!',
        ]);

        echo "Expanded Exemplar UAT Business Flow Seeder executed successfully!\n";
    }
}
