<?php

namespace App\Http\Controllers;

use App\Models\DetailPekerjaan;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class DetailPekerjaanController extends Controller
{
    // PB-06: Mahasiswa menyerahkan detail pekerjaan
    public function store(Request $request, Pesanan $pesanan)
    {
        $data = $request->validate([
            'dokumen' => 'nullable|file|max:10240',
            'instruksi_pengerjaan' => 'required|string',
            'referensi' => 'nullable|file|max:10240',
            'format_hasil' => 'nullable|string|max:50',
        ]);

        $dokumenPath = $request->hasFile('dokumen')
            ? $request->file('dokumen')->store('dokumen', 'public')
            : null;

        $referensiPath = $request->hasFile('referensi')
            ? $request->file('referensi')->store('referensi', 'public')
            : null;

        DetailPekerjaan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'dokumen' => $dokumenPath,
            'instruksi_pengerjaan' => $data['instruksi_pengerjaan'],
            'referensi' => $referensiPath,
            'format_hasil' => $data['format_hasil'] ?? null,
            'tanggal_upload' => now(),
            'status' => 'lengkap',
        ]);

        $pesanan->update(['status_pesanan' => 'dikerjakan']);

        return back()->with('status', 'Detail pekerjaan berhasil dikirim ke provider.');
    }
}
