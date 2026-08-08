<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\TrackingPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $provider = $request->user()->provider;

        abort_if(!$provider, 403, 'Data provider tidak ditemukan.');

        $status = $request->query('status');

        $pesanan = Pesanan::query()
            ->with([
                'mahasiswa',
                'layanan',
                'negosiasi',
                'pembayaran',
                'trackingTerbaru',
            ])
            ->where('id_provider', $provider->id_provider)
            ->when($status, function ($query) use ($status) {
                $query->where('status_pesanan', $status);
            })
            ->latest('tanggal_pesanan')
            ->paginate(10)
            ->withQueryString();

        return view('order', compact(
            'provider',
            'pesanan',
            'status'
        ));
    }

    public function show(Request $request, Pesanan $pesanan)
    {
        $this->authorizeProvider($request, $pesanan);

        $pesanan->load([
            'mahasiswa',
            'layanan',
            'negosiasi',
            'detailPekerjaan',
            'tracking' => function ($query) {
                $query->latest('tanggal_update');
            },
            'pembayaran',
            'review',
        ]);

        return view('orders.show', compact('pesanan'));
    }

    public function updateStatus(
        Request $request,
        Pesanan $pesanan
    ) {
        $this->authorizeProvider($request, $pesanan);

        $validated = $request->validate([
            'status_pesanan' => [
                'required',
                Rule::in([
                    'menunggu pembayaran',
                    'diproses',
                    'revisi',
                    'selesai',
                    'dibatalkan',
                ]),
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        DB::beginTransaction();

        try {
            $pesanan->update([
                'status_pesanan' => $validated['status_pesanan'],
            ]);

            TrackingPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'status_pengerjaan' => $validated['status_pesanan'],
                'deskripsi' => $validated['deskripsi']
                    ?? 'Status pesanan diperbarui.',
                'file_progress' => null,
                'tanggal_update' => now(),
            ]);

            DB::commit();

            return back()->with(
                'success',
                'Status pesanan berhasil diperbarui.'
            );
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return back()->with(
                'error',
                'Status pesanan gagal diperbarui.'
            );
        }
    }

    public function complete(
        Request $request,
        Pesanan $pesanan
    ) {
        $this->authorizeProvider($request, $pesanan);

        DB::beginTransaction();

        try {
            $pesanan->update([
                'status_pesanan' => 'selesai',
            ]);

            TrackingPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'status_pengerjaan' => 'selesai',
                'deskripsi' => 'Pekerjaan ditandai selesai oleh provider.',
                'file_progress' => null,
                'tanggal_update' => now(),
            ]);

            DB::commit();

            return back()->with(
                'success',
                'Pesanan berhasil ditandai selesai.'
            );
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return back()->with(
                'error',
                'Pesanan gagal diselesaikan.'
            );
        }
    }

    private function authorizeProvider(
        Request $request,
        Pesanan $pesanan
    ): void {
        $provider = $request->user()->provider;

        abort_if(
            !$provider ||
                $pesanan->id_provider !== $provider->id_provider,
            403,
            'Anda tidak memiliki akses ke pesanan ini.'
        );
    }
}
