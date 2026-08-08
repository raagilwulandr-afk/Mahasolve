<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\TrackingPesanan;
use Illuminate\Http\Request;

class TrackingPesananController extends Controller
{
    // PB-07: Provider memberi update progres / draft, mahasiswa memberi klarifikasi/revisi
    public function store(Request $request, Pesanan $pesanan)
    {
        $data = $request->validate([
            'status_pengerjaan' => 'required|string|max:150',
            'file_progress' => 'nullable|file|max:10240',
        ]);

        $filePath = $request->hasFile('file_progress')
            ? $request->file('file_progress')->store('progress', 'public')
            : null;

        TrackingPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'status_pengerjaan' => $data['status_pengerjaan'],
            'file_progress' => $filePath,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Update progres berhasil dikirim.');
    }
}
