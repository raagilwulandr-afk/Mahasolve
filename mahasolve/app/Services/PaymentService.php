<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\UploadedFile;

class PaymentService
{
    public function processPayment(Pesanan $pesanan, string $metodePembayaran, ?UploadedFile $uploadedProof = null): Pembayaran
    {
        $path = 'bukti-bayar/qris_simulasi.png';
        $statusBayar = 'dikonfirmasi';

        if ($uploadedProof) {
            $path = $uploadedProof->store('bukti-bayar', 'public');
            $statusBayar = 'dikonfirmasi';
        }

        $pembayaran = Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => $metodePembayaran,
            'total_bayar' => $pesanan->harga_final,
            'bukti_pembayaran' => $path,
            'status_bayar' => $statusBayar,
        ]);

        if ($statusBayar === 'dikonfirmasi') {
            $pesanan->update(['status_pesanan' => 'dikerjakan']);
        }

        return $pembayaran;
    }
}
