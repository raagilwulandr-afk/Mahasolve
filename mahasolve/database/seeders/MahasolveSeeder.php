<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasolveSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user')->insert([
            ['id_user' => 1, 'username' => 'raka_mhs', 'email' => 'raka.pratama@student.ac.id', 'password' => bcrypt('password123'), 'no_hp' => '081234567801', 'role' => 'mahasiswa', 'created_at' => now()],
            ['id_user' => 2, 'username' => 'dinda_mhs', 'email' => 'dinda.ayu@student.ac.id', 'password' => bcrypt('password123'), 'no_hp' => '081234567802', 'role' => 'mahasiswa', 'created_at' => now()],
            ['id_user' => 3, 'username' => 'fajar_mhs', 'email' => 'fajar.nugraha@student.ac.id', 'password' => bcrypt('password123'), 'no_hp' => '081234567803', 'role' => 'mahasiswa', 'created_at' => now()],
            ['id_user' => 4, 'username' => 'siti_desain', 'email' => 'siti.desain@gmail.com', 'password' => bcrypt('password123'), 'no_hp' => '081234567804', 'role' => 'provider', 'created_at' => now()],
            ['id_user' => 5, 'username' => 'budi_ketik', 'email' => 'budi.ketik@gmail.com', 'password' => bcrypt('password123'), 'no_hp' => '081234567805', 'role' => 'provider', 'created_at' => now()],
            ['id_user' => 6, 'username' => 'wulan_edit', 'email' => 'wulan.editvideo@gmail.com', 'password' => bcrypt('password123'), 'no_hp' => '081234567806', 'role' => 'provider', 'created_at' => now()],
            ['id_user' => 7, 'username' => 'andi_koding', 'email' => 'andi.koding@gmail.com', 'password' => bcrypt('password123'), 'no_hp' => '081234567807', 'role' => 'provider', 'created_at' => now()],
        ]);

        DB::table('provider')->insert([
            ['id_provider' => 1, 'id_user' => 4, 'rating' => 4.8, 'detail_provider' => 'Desainer grafis freelance, spesialis poster, banner, dan slide presentasi. Pengalaman 2 tahun.'],
            ['id_provider' => 2, 'id_user' => 5, 'rating' => 4.5, 'detail_provider' => 'Jasa pengetikan cepat & rapi untuk skripsi, makalah, dan laporan. Bisa revisi format IEEE/APA.'],
            ['id_provider' => 3, 'id_user' => 6, 'rating' => 4.9, 'detail_provider' => 'Editor video untuk konten TikTok, YouTube, dan tugas kuliah multimedia.'],
            ['id_provider' => 4, 'id_user' => 7, 'rating' => 4.7, 'detail_provider' => 'Mahasiswa Informatika, jasa pengerjaan tugas coding (Python, Java, Web).'],
        ]);

        DB::table('layanan')->insert([
            ['id_layanan' => 1, 'id_provider' => 1, 'nama_layanan' => 'Desain Poster Seminar', 'kategori' => 'Desain', 'deskripsi' => 'Desain poster seminar/event kampus, 2x revisi, format PNG/PDF.', 'harga' => 50000, 'estimasi_pengerjaan' => '2 hari'],
            ['id_layanan' => 2, 'id_provider' => 1, 'nama_layanan' => 'Desain Slide Presentasi', 'kategori' => 'Desain', 'deskripsi' => 'Desain ulang slide presentasi maks 20 halaman, tema custom.', 'harga' => 75000, 'estimasi_pengerjaan' => '3 hari'],
            ['id_layanan' => 3, 'id_provider' => 2, 'nama_layanan' => 'Jasa Ketik Skripsi', 'kategori' => 'Pengetikan', 'deskripsi' => 'Ketik ulang naskah tulisan tangan/foto ke Word, rapi sesuai format kampus.', 'harga' => 35000, 'estimasi_pengerjaan' => '2 hari'],
            ['id_layanan' => 4, 'id_provider' => 2, 'nama_layanan' => 'Jasa Ketik Makalah', 'kategori' => 'Pengetikan', 'deskripsi' => 'Ketik makalah 10-30 halaman sesuai referensi yang diberikan.', 'harga' => 25000, 'estimasi_pengerjaan' => '1 hari'],
            ['id_layanan' => 5, 'id_provider' => 3, 'nama_layanan' => 'Editing Video Tugas Kuliah', 'kategori' => 'Editing', 'deskripsi' => 'Edit video dokumentasi/tugas multimedia durasi maks 10 menit.', 'harga' => 60000, 'estimasi_pengerjaan' => '3 hari'],
            ['id_layanan' => 6, 'id_provider' => 4, 'nama_layanan' => 'Bantuan Tugas Pemrograman', 'kategori' => 'Coding', 'deskripsi' => 'Bantuan mengerjakan tugas praktikum coding (Python/Java/Web).', 'harga' => 80000, 'estimasi_pengerjaan' => '2 hari'],
        ]);

        DB::table('request_layanan')->insert([
            ['id_request' => 1, 'id_user' => 1, 'detail_kebutuhan' => 'Butuh desain poster untuk seminar proposal skripsi, tema akademik formal.', 'kategori' => 'Desain', 'harga_awal' => 40000, 'deadline' => '2026-08-05', 'tanggal_request' => '2026-07-25 09:15:00', 'status_request' => 'selesai', 'kriteria_output' => 'File PNG resolusi tinggi + PDF, ukuran A3'],
            ['id_request' => 2, 'id_user' => 2, 'detail_kebutuhan' => 'Minta bantuan ketik ulang draft skripsi bab 1-3 dari foto tulisan tangan.', 'kategori' => 'Pengetikan', 'harga_awal' => 30000, 'deadline' => '2026-08-10', 'tanggal_request' => '2026-07-26 14:30:00', 'status_request' => 'diproses', 'kriteria_output' => 'Format Word, font Times New Roman 12, spasi 1.5'],
            ['id_request' => 3, 'id_user' => 3, 'detail_kebutuhan' => 'Butuh edit video dokumentasi kegiatan organisasi kampus, durasi 8 menit.', 'kategori' => 'Editing', 'harga_awal' => 55000, 'deadline' => '2026-08-08', 'tanggal_request' => '2026-07-27 11:00:00', 'status_request' => 'open', 'kriteria_output' => 'Format MP4 1080p, ada intro & outro'],
            ['id_request' => 4, 'id_user' => 1, 'detail_kebutuhan' => 'Perlu bantuan tugas praktikum Python tentang struktur data.', 'kategori' => 'Coding', 'harga_awal' => 70000, 'deadline' => '2026-08-03', 'tanggal_request' => '2026-07-27 20:45:00', 'status_request' => 'open', 'kriteria_output' => 'Kode berjalan + penjelasan singkat tiap fungsi'],
        ]);

        DB::table('negosiasi')->insert([
            ['id_negosiasi' => 1, 'id_request' => 1, 'id_provider' => 1, 'harga_tawaran' => 45000, 'detail_negosiasi' => 'Provider menawar naik jadi 45rb karena revisi ditambah jadi 3x.', 'status_negosiasi' => 'disepakati', 'created_at' => '2026-07-25 10:00:00'],
            ['id_negosiasi' => 2, 'id_request' => 2, 'id_provider' => 2, 'harga_tawaran' => 32000, 'detail_negosiasi' => 'Mahasiswa setuju harga 32rb dengan tambahan waktu 1 hari.', 'status_negosiasi' => 'disepakati', 'created_at' => '2026-07-26 15:10:00'],
            ['id_negosiasi' => 3, 'id_request' => 3, 'id_provider' => 3, 'harga_tawaran' => 60000, 'detail_negosiasi' => 'Provider menawarkan harga sesuai list layanan, menunggu konfirmasi mahasiswa.', 'status_negosiasi' => 'pending', 'created_at' => '2026-07-27 11:20:00'],
            ['id_negosiasi' => 4, 'id_request' => 4, 'id_provider' => 4, 'harga_tawaran' => 65000, 'detail_negosiasi' => 'Mahasiswa menawar turun dari 80rb ke 65rb.', 'status_negosiasi' => 'ditawar_ulang', 'created_at' => '2026-07-27 21:00:00'],
        ]);

        DB::table('pesanan')->insert([
            ['id_pesanan' => 1, 'id_negosiasi' => 1, 'harga_final' => 45000, 'tanggal_pesanan' => '2026-07-25 10:05:00', 'status_pesanan' => 'selesai'],
            ['id_pesanan' => 2, 'id_negosiasi' => 2, 'harga_final' => 32000, 'tanggal_pesanan' => '2026-07-26 15:15:00', 'status_pesanan' => 'dikerjakan'],
        ]);

        DB::table('detail_pekerjaan')->insert([
            ['id_detail' => 1, 'id_pesanan' => 1, 'dokumen' => 'storage/dokumen/poster_brief_raka.pdf', 'instruksi_pengerjaan' => 'Tema formal warna biru-putih, logo kampus di pojok kanan atas.', 'referensi' => 'storage/referensi/contoh_poster.jpg', 'format_hasil' => 'PNG, PDF', 'tanggal_upload' => '2026-07-25 10:10:00', 'status' => 'lengkap'],
            ['id_detail' => 2, 'id_pesanan' => 2, 'dokumen' => 'storage/dokumen/skripsi_bab1-3_dinda.jpg', 'instruksi_pengerjaan' => 'Tolong perhatikan penomoran halaman romawi di bagian awal.', 'referensi' => null, 'format_hasil' => 'DOCX', 'tanggal_upload' => '2026-07-26 15:20:00', 'status' => 'lengkap'],
        ]);

        DB::table('tracking_pesanan')->insert([
            ['id_tracking' => 1, 'id_pesanan' => 1, 'status_pengerjaan' => 'Draft awal selesai, menunggu review mahasiswa', 'file_progress' => 'storage/progress/poster_draft1.png', 'created_at' => '2026-07-25 16:00:00'],
            ['id_tracking' => 2, 'id_pesanan' => 1, 'status_pengerjaan' => 'Revisi warna & logo selesai, hasil final diupload', 'file_progress' => 'storage/progress/poster_final.png', 'created_at' => '2026-07-26 09:30:00'],
            ['id_tracking' => 3, 'id_pesanan' => 2, 'status_pengerjaan' => 'Bab 1 selesai diketik, lanjut bab 2', 'file_progress' => null, 'created_at' => '2026-07-27 08:00:00'],
        ]);

        DB::table('pembayaran')->insert([
            ['id_pembayaran' => 1, 'id_pesanan' => 1, 'metode_pembayaran' => 'Transfer Bank', 'total_bayar' => 45000, 'bukti_pembayaran' => 'storage/bukti_bayar/bukti_raka.jpg', 'status_bayar' => 'dikonfirmasi'],
            ['id_pembayaran' => 2, 'id_pesanan' => 2, 'metode_pembayaran' => 'E-Wallet', 'total_bayar' => 32000, 'bukti_pembayaran' => null, 'status_bayar' => 'menunggu_konfirmasi'],
        ]);

        DB::table('rating_review')->insert([
            ['id_review' => 1, 'id_pesanan' => 1, 'review' => 'Hasil poster bagus, sesuai brief, dan pengerjaannya cepat!', 'rate' => 5],
        ]);
    }
}
