<?php

namespace App\Services;

use App\Models\DetailPekerjaan;
use App\Models\Pesanan;
use App\Models\TrackingPesanan;
use Illuminate\Http\UploadedFile;

class TrackingService
{
    public function submitDetailPekerjaan(Pesanan $pesanan, string $instruksiPengerjaan, ?string $formatHasil = null, ?UploadedFile $dokumenFile = null, ?UploadedFile $referensiFile = null): DetailPekerjaan
    {
        $dokumenPath = $dokumenFile ? $dokumenFile->store('dokumen', 'public') : null;
        $referensiPath = $referensiFile ? $referensiFile->store('referensi', 'public') : null;

        $detailPekerjaan = DetailPekerjaan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'dokumen' => $dokumenPath,
            'instruksi_pengerjaan' => $instruksiPengerjaan,
            'referensi' => $referensiPath,
            'format_hasil' => $formatHasil,
            'tanggal_upload' => now(),
            'status' => 'lengkap',
        ]);

        $pesanan->update(['status_pesanan' => 'dikerjakan']);

        return $detailPekerjaan;
    }

    public function addProgressLog(Pesanan $pesanan, string $statusPengerjaan, ?UploadedFile $fileProgress = null): TrackingPesanan
    {
        $filePath = $fileProgress ? $fileProgress->store('progress', 'public') : null;

        return TrackingPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'status_pengerjaan' => $statusPengerjaan,
            'file_progress' => $filePath,
            'created_at' => now(),
        ]);
    }
}
