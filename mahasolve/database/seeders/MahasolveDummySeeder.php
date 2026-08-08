<?php

namespace Database\Seeders;

use App\Models\DetailPekerjaan;
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

class MahasolveDummySeeder extends Seeder
{
    public function run(): void
    {
        // ================= DT1: user =================
        $raka  = User::create(['username' => 'raka_mhs', 'email' => 'raka.pratama@student.ac.id', 'password' => Hash::make('password123'), 'no_hp' => '081234567801', 'role' => 'mahasiswa']);
        $dinda = User::create(['username' => 'dinda_mhs', 'email' => 'dinda.ayu@student.ac.id', 'password' => Hash::make('password123'), 'no_hp' => '081234567802', 'role' => 'mahasiswa']);
        $fajar = User::create(['username' => 'fajar_mhs', 'email' => 'fajar.nugraha@student.ac.id', 'password' => Hash::make('password123'), 'no_hp' => '081234567803', 'role' => 'mahasiswa']);

        $rizky = User::create(['username' => 'rizky_antar', 'email' => 'rizky.maulana@gmail.com', 'password' => Hash::make('password123'), 'no_hp' => '081234567804', 'role' => 'provider']);
        $siti  = User::create(['username' => 'siti_desain', 'email' => 'siti.desain@gmail.com', 'password' => Hash::make('password123'), 'no_hp' => '081234567805', 'role' => 'provider']);
        $budi  = User::create(['username' => 'budi_print', 'email' => 'budi.print@gmail.com', 'password' => Hash::make('password123'), 'no_hp' => '081234567806', 'role' => 'provider']);
        $wulan = User::create(['username' => 'wulan_edit', 'email' => 'wulan.editvideo@gmail.com', 'password' => Hash::make('password123'), 'no_hp' => '081234567807', 'role' => 'provider']);
        $andi  = User::create(['username' => 'andi_bimbingan', 'email' => 'andi.bimbingan@gmail.com', 'password' => Hash::make('password123'), 'no_hp' => '081234567808', 'role' => 'provider']);
        $alya  = User::create(['username' => 'alya_desain', 'email' => 'alya.rahmawati@gmail.com', 'password' => Hash::make('password123'), 'no_hp' => '081234567809', 'role' => 'provider']);
        $dewi  = User::create(['username' => 'dewi_titipmakan', 'email' => 'dewi.anjani@gmail.com', 'password' => Hash::make('password123'), 'no_hp' => '081234567810', 'role' => 'provider']);
        $yusuf = User::create(['username' => 'yusuf_titipbeli', 'email' => 'yusuf.pratama@gmail.com', 'password' => Hash::make('password123'), 'no_hp' => '081234567811', 'role' => 'provider']);

        // ================= DT2: provider =================
        $pRizky = Provider::create(['id_user' => $rizky->id_user, 'rating' => 4.9, 'detail_provider' => 'Antar jemput area Dipatiukur & sekitar. Cepat dan aman.']);
        $pSiti  = Provider::create(['id_user' => $siti->id_user, 'rating' => 4.8, 'detail_provider' => 'Desainer grafis freelance, spesialis poster, banner, dan slide presentasi.']);
        $pBudi  = Provider::create(['id_user' => $budi->id_user, 'rating' => 4.5, 'detail_provider' => 'Jasa print, fotokopi, dan jilid laporan/skripsi. Lokasi dekat kampus.']);
        $pWulan = Provider::create(['id_user' => $wulan->id_user, 'rating' => 4.9, 'detail_provider' => 'Editor video untuk konten TikTok, YouTube, dan tugas kuliah multimedia.']);
        $pAndi  = Provider::create(['id_user' => $andi->id_user, 'rating' => 4.7, 'detail_provider' => 'Tutor sebaya untuk struktur data, algoritma, dan pemrograman.']);
        $pAlya  = Provider::create(['id_user' => $alya->id_user, 'rating' => 4.8, 'detail_provider' => 'Desain slide presentasi & sidang, tema custom sesuai kebutuhan.']);
        $pDewi  = Provider::create(['id_user' => $dewi->id_user, 'rating' => 4.6, 'detail_provider' => 'Jasa titip makan siang area kantin TC & sekitar kampus.']);
        $pYusuf = Provider::create(['id_user' => $yusuf->id_user, 'rating' => 4.4, 'detail_provider' => 'Titip beli kebutuhan harian & alat tulis di sekitar kampus.']);

        // ================= DT3: layanan (6 kategori resmi) =================
        Layanan::create(['id_provider' => $pRizky->id_provider, 'nama_layanan' => 'Antar Jemput Motor', 'kategori' => 'Antar Jemput', 'deskripsi' => 'Antar jemput cepat naik motor, area sekitar kampus.', 'harga' => 8000, 'estimasi_pengerjaan' => '15 menit']);
        Layanan::create(['id_provider' => $pRizky->id_provider, 'nama_layanan' => 'Antar Jemput Mobil', 'kategori' => 'Antar Jemput', 'deskripsi' => 'Antar jemput naik mobil, muat sampai 3 orang.', 'harga' => 15000, 'estimasi_pengerjaan' => '20 menit']);

        Layanan::create(['id_provider' => $pSiti->id_provider, 'nama_layanan' => 'Desain Poster Seminar', 'kategori' => 'Desain & Editing', 'deskripsi' => 'Desain poster seminar/event kampus, 2x revisi, format PNG/PDF.', 'harga' => 50000, 'estimasi_pengerjaan' => '2 hari']);
        Layanan::create(['id_provider' => $pSiti->id_provider, 'nama_layanan' => 'Desain Slide Presentasi', 'kategori' => 'Desain & Editing', 'deskripsi' => 'Desain ulang slide presentasi maks 20 halaman, tema custom.', 'harga' => 75000, 'estimasi_pengerjaan' => '3 hari']);
        Layanan::create(['id_provider' => $pAlya->id_provider, 'nama_layanan' => 'Desain PPT Sidang', 'kategori' => 'Desain & Editing', 'deskripsi' => 'Desain slide sidang/presentasi skripsi, rapi & profesional.', 'harga' => 70000, 'estimasi_pengerjaan' => '2 hari']);
        Layanan::create(['id_provider' => $pWulan->id_provider, 'nama_layanan' => 'Editing Video Tugas Kuliah', 'kategori' => 'Desain & Editing', 'deskripsi' => 'Edit video dokumentasi/tugas multimedia durasi maks 10 menit.', 'harga' => 60000, 'estimasi_pengerjaan' => '3 hari']);

        Layanan::create(['id_provider' => $pBudi->id_provider, 'nama_layanan' => 'Print & Jilid Laporan/Skripsi', 'kategori' => 'Print & Fotokopi', 'deskripsi' => 'Print, jilid, dan fotokopi laporan/skripsi rapi & rapi.', 'harga' => 27000, 'estimasi_pengerjaan' => '1 hari']);
        Layanan::create(['id_provider' => $pBudi->id_provider, 'nama_layanan' => 'Fotokopi & Print Makalah', 'kategori' => 'Print & Fotokopi', 'deskripsi' => 'Print & fotokopi makalah 10-30 halaman.', 'harga' => 15000, 'estimasi_pengerjaan' => '1 hari']);

        Layanan::create(['id_provider' => $pAndi->id_provider, 'nama_layanan' => 'Bimbingan Struktur Data', 'kategori' => 'Bimbingan', 'deskripsi' => 'Bimbingan 1-on-1 materi struktur data & algoritma, 2 jam sesi.', 'harga' => 70000, 'estimasi_pengerjaan' => '2 jam']);
        Layanan::create(['id_provider' => $pAndi->id_provider, 'nama_layanan' => 'Bantuan Tugas Pemrograman', 'kategori' => 'Bimbingan', 'deskripsi' => 'Bantuan mengerjakan tugas praktikum coding (Python/Java/Web).', 'harga' => 80000, 'estimasi_pengerjaan' => '2 hari']);

        Layanan::create(['id_provider' => $pDewi->id_provider, 'nama_layanan' => 'Titip Makan Siang Kantin', 'kategori' => 'Titip Makan', 'deskripsi' => 'Jasa titip makan siang area kantin TC & sekitarnya.', 'harga' => 5000, 'estimasi_pengerjaan' => '30 menit']);

        Layanan::create(['id_provider' => $pYusuf->id_provider, 'nama_layanan' => 'Titip Beli Kebutuhan Harian', 'kategori' => 'Titip Beli', 'deskripsi' => 'Belikan kebutuhan harian, alat tulis, atau jajan di minimarket kampus.', 'harga' => 10000, 'estimasi_pengerjaan' => '1 jam']);

        // ================= DT4: request_layanan =================
        $req1 = RequestLayanan::create(['id_user' => $raka->id_user, 'detail_kebutuhan' => 'Antar jemput ke Stasiun Bandung', 'kategori' => 'Antar Jemput', 'harga_awal' => 8000, 'deadline' => '2026-07-31', 'tanggal_request' => '2026-07-29 08:00:00', 'status_request' => 'diproses', 'kriteria_output' => 'Tepat waktu, motor']);
        $req2 = RequestLayanan::create(['id_user' => $dinda->id_user, 'detail_kebutuhan' => 'Desain PPT sidang 20 slide', 'kategori' => 'Desain & Editing', 'harga_awal' => 65000, 'deadline' => '2026-08-02', 'tanggal_request' => '2026-07-28 13:00:00', 'status_request' => 'diproses', 'kriteria_output' => 'Tema formal, format PPTX']);
        $req3 = RequestLayanan::create(['id_user' => $fajar->id_user, 'detail_kebutuhan' => 'Print + jilid laporan KP 3x', 'kategori' => 'Print & Fotokopi', 'harga_awal' => 27000, 'deadline' => '2026-07-22', 'tanggal_request' => '2026-07-21 09:00:00', 'status_request' => 'selesai', 'kriteria_output' => 'Jilid lakban, 3 rangkap']);
        $req4 = RequestLayanan::create(['id_user' => $raka->id_user, 'detail_kebutuhan' => 'Titip makan siang kantin TC', 'kategori' => 'Titip Makan', 'harga_awal' => 22000, 'deadline' => '2026-07-21', 'tanggal_request' => '2026-07-21 11:00:00', 'status_request' => 'selesai', 'kriteria_output' => 'Nasi + ayam, jangan pedas']);
        $req5 = RequestLayanan::create(['id_user' => $dinda->id_user, 'detail_kebutuhan' => 'Bimbingan Struktur Data 2 jam', 'kategori' => 'Bimbingan', 'harga_awal' => 70000, 'deadline' => '2026-07-18', 'tanggal_request' => '2026-07-17 15:00:00', 'status_request' => 'selesai', 'kriteria_output' => 'Fokus materi linked list & tree']);

        // ================= DT6: negosiasi =================
        $neg1 = Negosiasi::create(['id_request' => $req1->id_request, 'id_provider' => $pRizky->id_provider, 'harga_tawaran' => 8000, 'detail_negosiasi' => 'Deal langsung sesuai harga layanan.', 'dibuat_oleh' => 'mahasiswa', 'status_negosiasi' => 'disepakati', 'created_at' => '2026-07-29 08:05:00']);

        // Thread chat Dinda <-> Alya (Desain PPT sidang) — skenario percakapan lengkap
        Negosiasi::create(['id_request' => $req2->id_request, 'id_provider' => $pAlya->id_provider, 'harga_tawaran' => 65000, 'detail_negosiasi' => 'Halo kak, mau tanya kalau desain PPT sidang 20 slide berapa ya?', 'dibuat_oleh' => 'mahasiswa', 'status_negosiasi' => 'ditawar_ulang', 'created_at' => '2026-07-28 09:40:00']);
        Negosiasi::create(['id_request' => $req2->id_request, 'id_provider' => $pAlya->id_provider, 'harga_tawaran' => 65000, 'detail_negosiasi' => 'Halo! Terima kasih sudah menghubungi. Untuk desain PPT sidang biasanya saya kerjakan 2-3 hari kerja ya kak.', 'dibuat_oleh' => 'provider', 'status_negosiasi' => 'ditawar_ulang', 'created_at' => '2026-07-28 09:41:00']);
        Negosiasi::create(['id_request' => $req2->id_request, 'id_provider' => $pAlya->id_provider, 'harga_tawaran' => 65000, 'detail_negosiasi' => 'Iya kak, butuh yang rapi & ada animasi ringan. Budget segini bisa nggak?', 'dibuat_oleh' => 'mahasiswa', 'status_negosiasi' => 'ditawar_ulang', 'created_at' => '2026-07-28 09:42:00']);
        Negosiasi::create(['id_request' => $req2->id_request, 'id_provider' => $pAlya->id_provider, 'harga_tawaran' => 50000, 'detail_negosiasi' => 'Untuk 20 slide dengan revisi 2x, biasanya Rp50.000 kak.', 'dibuat_oleh' => 'provider', 'status_negosiasi' => 'ditawar_ulang', 'created_at' => '2026-07-28 09:43:00']);
        Negosiasi::create(['id_request' => $req2->id_request, 'id_provider' => $pAlya->id_provider, 'harga_tawaran' => 45000, 'detail_negosiasi' => 'Lusa sore bisa? Kalau Rp45.000 gimana kak? 🙏', 'dibuat_oleh' => 'mahasiswa', 'status_negosiasi' => 'ditawar_ulang', 'created_at' => '2026-07-28 09:44:00']);
        $neg2 = Negosiasi::create(['id_request' => $req2->id_request, 'id_provider' => $pAlya->id_provider, 'harga_tawaran' => 45000, 'detail_negosiasi' => 'Boleh, Rp45.000 deal untuk 20 slide + revisi 2x. Saya mulai besok ya kak.', 'dibuat_oleh' => 'provider', 'status_negosiasi' => 'pending', 'created_at' => '2026-07-28 09:45:00']);
        // neg2 sengaja masih 'pending' & belum jadi pesanan -> di halaman chat tampil sebagai kartu "Penawaran Harga" menunggu keputusan mahasiswa

        $neg3 = Negosiasi::create(['id_request' => $req3->id_request, 'id_provider' => $pBudi->id_provider, 'harga_tawaran' => 27000, 'detail_negosiasi' => 'Deal sesuai harga layanan.', 'dibuat_oleh' => 'mahasiswa', 'status_negosiasi' => 'disepakati', 'created_at' => '2026-07-21 09:05:00']);
        $neg4 = Negosiasi::create(['id_request' => $req4->id_request, 'id_provider' => $pDewi->id_provider, 'harga_tawaran' => 22000, 'detail_negosiasi' => 'Termasuk harga makanan + ongkos titip.', 'dibuat_oleh' => 'mahasiswa', 'status_negosiasi' => 'disepakati', 'created_at' => '2026-07-21 11:05:00']);
        $neg5 = Negosiasi::create(['id_request' => $req5->id_request, 'id_provider' => $pAndi->id_provider, 'harga_tawaran' => 70000, 'detail_negosiasi' => 'Deal sesuai harga layanan.', 'dibuat_oleh' => 'mahasiswa', 'status_negosiasi' => 'disepakati', 'created_at' => '2026-07-17 15:05:00']);

        // ================= DT7: pesanan =================
        $pesanan1 = Pesanan::create(['id_negosiasi' => $neg1->id_negosiasi, 'harga_final' => 8000, 'tanggal_pesanan' => '2026-07-29 08:10:00', 'status_pesanan' => 'dikerjakan']);
        $pesanan3 = Pesanan::create(['id_negosiasi' => $neg3->id_negosiasi, 'harga_final' => 27000, 'tanggal_pesanan' => '2026-07-22 09:00:00', 'status_pesanan' => 'selesai']);
        $pesanan4 = Pesanan::create(['id_negosiasi' => $neg4->id_negosiasi, 'harga_final' => 22000, 'tanggal_pesanan' => '2026-07-21 11:30:00', 'status_pesanan' => 'selesai']);
        $pesanan5 = Pesanan::create(['id_negosiasi' => $neg5->id_negosiasi, 'harga_final' => 70000, 'tanggal_pesanan' => '2026-07-18 10:00:00', 'status_pesanan' => 'selesai']);
        // neg2 (Alya) sengaja belum jadi pesanan -> masih tampil sebagai "Negosiasi" di Pesanan Aktif

        // ================= DT8: detail_pekerjaan =================
        DetailPekerjaan::create(['id_pesanan' => $pesanan1->id_pesanan, 'instruksi_pengerjaan' => 'Jemput di Gerbang Unikom jam 15.00, tujuan Stasiun Bandung.', 'format_hasil' => '-', 'tanggal_upload' => '2026-07-29 08:12:00', 'status' => 'lengkap']);

        // ================= DT9: tracking_pesanan =================
        TrackingPesanan::create(['id_pesanan' => $pesanan1->id_pesanan, 'status_pengerjaan' => 'Provider dalam perjalanan menuju titik jemput', 'created_at' => '2026-07-29 14:50:00']);

        // ================= DT10: pembayaran =================
        Pembayaran::create(['id_pesanan' => $pesanan3->id_pesanan, 'metode_pembayaran' => 'Transfer Bank', 'total_bayar' => 27000, 'status_bayar' => 'dikonfirmasi']);
        Pembayaran::create(['id_pesanan' => $pesanan4->id_pesanan, 'metode_pembayaran' => 'E-Wallet', 'total_bayar' => 22000, 'status_bayar' => 'dikonfirmasi']);
        Pembayaran::create(['id_pesanan' => $pesanan5->id_pesanan, 'metode_pembayaran' => 'Transfer Bank', 'total_bayar' => 70000, 'status_bayar' => 'dikonfirmasi']);

        // ================= DT11: rating_review =================
        RatingReview::create(['id_pesanan' => $pesanan3->id_pesanan, 'review' => 'Cepat dan hasil jilidnya rapi!', 'rate' => 5]);
        RatingReview::create(['id_pesanan' => $pesanan4->id_pesanan, 'review' => 'Makanannya sesuai request, on time.', 'rate' => 4]);
        RatingReview::create(['id_pesanan' => $pesanan5->id_pesanan, 'review' => 'Penjelasannya gampang dipahami, makasih kak!', 'rate' => 5]);

        $pBudi->refreshRating();
        $pDewi->refreshRating();
        $pAndi->refreshRating();
    }
}
