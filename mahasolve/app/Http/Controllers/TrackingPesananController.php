<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\TrackingService;
use Illuminate\Http\Request;

class TrackingPesananController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    public function store(Request $request, Pesanan $pesanan)
    {
        $data = $request->validate([
            'status_pengerjaan' => 'required|string|max:150',
            'file_progress' => 'nullable|file|max:10240',
        ]);

        $this->trackingService->addProgressLog(
            pesanan: $pesanan,
            statusPengerjaan: $data['status_pengerjaan'],
            fileProgress: $request->file('file_progress')
        );

        return back()->with('success', 'Update progres berhasil dikirim.');
    }
}
