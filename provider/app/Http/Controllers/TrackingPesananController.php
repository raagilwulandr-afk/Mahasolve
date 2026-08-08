<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\TrackingPesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrackingPesananController extends Controller
{
    public function store(
        Request $request,
        Pesanan $pesanan
    ) {
        $provider = $request->user()->provider;

        abort_if(
            !$provider ||
                $pesanan->id_provider !== $provider->id_provider,
            403,
            'Anda tidak memiliki akses ke pesanan ini.'
        );

        $validated = $request->validate([
            'status_pengerjaan' => [
                'required',
                Rule::in([
                    'menunggu pembayaran',
                    'diproses',
                    'revisi',
                    'selesai',
                ]),
            ],
            'deskripsi' => [
                'required',
                'string',
                'max:2000',
            ],
            'file_progress' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar',
                'max:10240',
            ],
        ]);

        $filePath = null;

        if ($request->hasFile('file_progress')) {
            $filePath = $request
                ->file('file_progress')
                ->store('progress', 'public');
        }

        TrackingPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'status_pengerjaan' => $validated['status_pengerjaan'],
            'deskripsi' => $validated['deskripsi'],
            'file_progress' => $filePath,
            'tanggal_update' => now(),
        ]);

        $pesanan->update([
            'status_pesanan' => $validated['status_pengerjaan'],
        ]);

        return back()->with(
            'success',
            'Progress pekerjaan berhasil ditambahkan.'
        );
    }
}
