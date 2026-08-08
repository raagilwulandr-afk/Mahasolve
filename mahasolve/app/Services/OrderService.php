<?php

namespace App\Services;

use App\Models\DetailPekerjaan;
use App\Models\Layanan;
use App\Models\Negosiasi;
use App\Models\Pesanan;
use App\Models\RequestLayanan;
use App\Models\TrackingPesanan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createDirectOrder(User $user, int $layananId, ?string $catatan = null): Pesanan
    {
        $layanan = Layanan::with('provider')->findOrFail($layananId);
        $pesanan = null;

        DB::transaction(function () use ($user, $layanan, $catatan, &$pesanan) {
            $reqLayanan = RequestLayanan::create([
                'id_user' => $user->id_user,
                'kategori' => $layanan->kategori,
                'detail_kebutuhan' => $layanan->nama_layanan . ($catatan ? " ({$catatan})" : ""),
                'harga_awal' => $layanan->harga,
                'status_request' => 'diproses',
            ]);

            $nego = Negosiasi::create([
                'id_request' => $reqLayanan->id_request,
                'id_provider' => $layanan->id_provider,
                'harga_tawaran' => $layanan->harga,
                'detail_negosiasi' => 'Pembelian langsung dari Katalog Layanan (Instant Checkout)',
                'dibuat_oleh' => 'mahasiswa',
                'status_negosiasi' => 'disepakati',
            ]);

            $pesanan = Pesanan::create([
                'id_negosiasi' => $nego->id_negosiasi,
                'harga_final' => $layanan->harga,
                'tanggal_pesanan' => now(),
                'status_pesanan' => 'menunggu_pengerjaan',
            ]);

            TrackingPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'status_pengerjaan' => 'Pesanan berhasil dibuat dari Katalog. Menunggu pengerjaan provider.',
            ]);
        });

        return $pesanan;
    }

    public function updateProgress(Pesanan $pesanan, string $statusInput, ?string $pesanProgress = null, ?string $dokumenPath = null, ?UploadedFile $uploadedFile = null): Pesanan
    {
        $statusPesanan = ($statusInput === 'diproses' || $statusInput === 'dikerjakan') ? 'dikerjakan' : 'selesai';
        $pesanan->update(['status_pesanan' => $statusPesanan]);

        if ($uploadedFile) {
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $uploadedFile->getClientOriginalName());
            $uploadDir = public_path('uploads/deliverables');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $uploadedFile->move($uploadDir, $filename);
            $dokumenPath = asset('uploads/deliverables/' . $filename);
        }

        TrackingPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'status_pengerjaan' => $pesanProgress ?? ("Status pesanan diperbarui menjadi: " . ucfirst($statusPesanan)),
            'file_progress' => $dokumenPath,
        ]);

        if ($dokumenPath) {
            DetailPekerjaan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'dokumen' => $dokumenPath,
                'instruksi_pengerjaan' => $pesanProgress ?? 'Hasil pekerjaan telah diunggah oleh provider.',
                'format_hasil' => 'File / Link Hasil Pekerjaan',
                'tanggal_upload' => now(),
                'status' => 'lengkap',
            ]);
        }

        return $pesanan;
    }
}
