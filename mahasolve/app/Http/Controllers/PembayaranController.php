<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    // Mahasiswa/Client mengirim pembayaran QRIS
    public function store(Request $request, Pesanan $pesanan)
    {
        $user = auth()->user();
        abort_unless((int) $pesanan->negosiasi->request->id_user === (int) $user->id_user, 403);
        
        if ($pesanan->pembayaran) {
            return back()->with('success', 'Pembayaran untuk pesanan ini sudah berhasil dikonfirmasi.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => 'required|string|max:50',
            'bukti_pembayaran' => 'nullable|file|image|max:5120', // 5MB
        ]);

        $path = 'bukti-bayar/qris_simulasi.png';
        $statusBayar = 'dikonfirmasi';

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti-bayar', 'public');
            $statusBayar = 'menunggu_konfirmasi';
        }

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'total_bayar' => $pesanan->harga_final,
            'bukti_pembayaran' => $path,
            'status_bayar' => $statusBayar,
        ]);

        // Update status pesanan ke dikerjakan jika pembayaran dikonfirmasi
        if ($statusBayar === 'dikonfirmasi') {
            $pesanan->update(['status_pesanan' => 'dikerjakan']);
        }

        return back()->with('success', 'Pembayaran QRIS berhasil! Pesanan kamu sedang diproses oleh mitra.');
    }
}
