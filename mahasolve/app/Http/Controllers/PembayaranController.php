<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Pesanan;
use App\Services\PaymentService;

class PembayaranController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function store(StorePaymentRequest $request, Pesanan $pesanan)
    {
        $user = auth()->user();
        abort_unless((int) $pesanan->negosiasi->request->id_user === (int) $user->id_user, 403);

        if ($pesanan->pembayaran) {
            return back()->with('success', 'Pembayaran untuk pesanan ini sudah berhasil dikonfirmasi.');
        }

        $this->paymentService->processPayment(
            pesanan: $pesanan,
            metodePembayaran: $request->validated('metode_pembayaran'),
            uploadedProof: $request->file('bukti_pembayaran')
        );

        return back()->with('success', 'Pembayaran QRIS berhasil! Pesanan kamu sedang diproses oleh mitra.');
    }
}
