<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\TrackingService;
use Illuminate\Http\Request;

class DetailPekerjaanController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    public function store(Request $request, Pesanan $pesanan)
    {
        $data = $request->validate([
            'dokumen' => 'nullable|file|max:10240',
            'instruksi_pengerjaan' => 'required|string',
            'referensi' => 'nullable|file|max:10240',
            'format_hasil' => 'nullable|string|max:50',
        ]);

        $this->trackingService->submitDetailPekerjaan(
            pesanan: $pesanan,
            instruksiPengerjaan: $data['instruksi_pengerjaan'],
            formatHasil: $data['format_hasil'] ?? null,
            dokumenFile: $request->file('dokumen'),
            referensiFile: $request->file('referensi')
        );

        return back()->with('success', 'Detail pekerjaan berhasil dikirim ke provider.');
    }
}
