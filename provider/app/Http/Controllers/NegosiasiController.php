<?php

namespace App\Http\Controllers;

use App\Models\MatchingProvider;
use App\Models\Negosiasi;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class NegosiasiController extends Controller
{
    public function store(
        Request $request,
        MatchingProvider $matching
    ) {
        $provider = $request->user()->provider;

        abort_if(
            !$provider ||
                $matching->id_provider !== $provider->id_provider,
            403,
            'Anda tidak memiliki akses ke permintaan ini.'
        );

        $matching->load('requestLayanan');

        abort_if(
            !$matching->requestLayanan,
            404,
            'Data request tidak ditemukan.'
        );

        $validated = $request->validate([
            'id_layanan' => [
                'required',
                Rule::exists('layanan', 'id_layanan')
                    ->where(
                        'id_provider',
                        $provider->id_provider
                    ),
            ],
            'penawaran_harga' => [
                'required',
                'numeric',
                'min:0',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $negosiasi = Negosiasi::updateOrCreate(
            [
                'id_matching' => $matching->id_matching,
            ],
            [
                'id_request' => $matching->id_request,
                'id_layanan' => $validated['id_layanan'],
                'id_provider' => $provider->id_provider,
                'penawaran_harga' => $validated['penawaran_harga'],
                'catatan' => $validated['catatan'] ?? null,
                'status_negosiasi' => 'ditawar',
                'tanggal_negosiasi' => now(),
            ]
        );

        $matching->requestLayanan()->update([
            'status_request' => 'dinegosiasikan',
        ]);

        return redirect()
            ->route('provider.requests.show', $matching)
            ->with(
                'success',
                'Penawaran harga berhasil dikirim.'
            );
    }

    public function updateStatus(
        Request $request,
        Negosiasi $negosiasi
    ) {
        $provider = $request->user()->provider;

        abort_if(
            !$provider ||
                $negosiasi->id_provider !== $provider->id_provider,
            403,
            'Anda tidak memiliki akses ke negosiasi ini.'
        );

        $validated = $request->validate([
            'status_negosiasi' => [
                'required',
                Rule::in([
                    'menunggu',
                    'ditawar',
                    'disepakati',
                    'ditolak',
                ]),
            ],
            'penawaran_harga' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        DB::beginTransaction();

        try {
            $negosiasi->update([
                'status_negosiasi' => $validated['status_negosiasi'],
                'penawaran_harga' => $validated['penawaran_harga']
                    ?? $negosiasi->penawaran_harga,
                'catatan' => $validated['catatan']
                    ?? $negosiasi->catatan,
                'tanggal_negosiasi' => now(),
            ]);

            if ($validated['status_negosiasi'] === 'disepakati') {
                $this->createOrder($negosiasi);
            }

            if ($validated['status_negosiasi'] === 'ditolak') {
                $negosiasi->requestLayanan()->update([
                    'status_request' => 'batal',
                ]);
            }

            DB::commit();

            return back()->with(
                'success',
                'Status negosiasi berhasil diperbarui.'
            );
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return back()->with(
                'error',
                'Status negosiasi gagal diperbarui.'
            );
        }
    }

    private function createOrder(Negosiasi $negosiasi): Pesanan
    {
        $negosiasi->load('requestLayanan');

        $requestLayanan = $negosiasi->requestLayanan;

        $pesanan = Pesanan::firstOrCreate(
            [
                'id_negosiasi' => $negosiasi->id_negosiasi,
            ],
            [
                'id_user' => $requestLayanan->id_user,
                'id_provider' => $negosiasi->id_provider,
                'id_layanan' => $negosiasi->id_layanan,
                'harga_final' => $negosiasi->penawaran_harga,
                'tanggal_pesanan' => now(),
                'status_pesanan' => 'menunggu pembayaran',
                'total_harga' => $negosiasi->penawaran_harga,
            ]
        );

        $requestLayanan->update([
            'status_request' => 'selesai',
        ]);

        return $pesanan;
    }
}
