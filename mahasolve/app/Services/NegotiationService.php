<?php

namespace App\Services;

use App\Models\Negosiasi;
use App\Models\Pesanan;
use App\Models\Provider;
use App\Models\RequestLayanan;
use App\Models\TrackingPesanan;
use Illuminate\Support\Facades\DB;

class NegotiationService
{
    public function acceptNegotiation(Negosiasi $negosiasi): Pesanan
    {
        $terakhir = Negosiasi::where('id_request', $negosiasi->id_request)
            ->where('id_provider', $negosiasi->id_provider)
            ->latest('created_at')
            ->first();

        abort_if($terakhir->status_negosiasi === 'disepakati', 400, 'Sudah disepakati sebelumnya.');

        $pesanan = null;

        DB::transaction(function () use ($terakhir, $negosiasi, &$pesanan) {
            $terakhir->update(['status_negosiasi' => 'disepakati']);

            $pesanan = Pesanan::create([
                'id_negosiasi' => $terakhir->id_negosiasi,
                'harga_final' => $terakhir->harga_tawaran,
                'tanggal_pesanan' => now(),
                'status_pesanan' => 'menunggu_pengerjaan',
            ]);

            TrackingPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'status_pengerjaan' => 'Negosiasi disepakati. Pesanan dalam antrean pengerjaan.',
            ]);

            $negosiasi->request->update(['status_request' => 'diproses']);
        });

        return $pesanan;
    }

    public function counterOffer(Provider $provider, int $id, float|int $hargaTawaran, ?string $pesan = null): Negosiasi
    {
        $nego = Negosiasi::where('id_provider', $provider->id_provider)
            ->where(function ($q) use ($id) {
                $q->where('id_negosiasi', $id)->orWhere('id_request', $id);
            })
            ->latest()
            ->first();

        if (!$nego) {
            $reqLayanan = RequestLayanan::findOrFail($id);
            return Negosiasi::create([
                'id_request' => $reqLayanan->id_request,
                'id_provider' => $provider->id_provider,
                'harga_tawaran' => $hargaTawaran,
                'detail_negosiasi' => $pesan ?? 'Penawaran balik dari provider.',
                'dibuat_oleh' => 'provider',
                'status_negosiasi' => 'ditawar_ulang',
            ]);
        }

        $newNego = Negosiasi::create([
            'id_request' => $nego->id_request,
            'id_provider' => $provider->id_provider,
            'harga_tawaran' => $hargaTawaran,
            'detail_negosiasi' => $pesan ?? 'Penawaran balik dari provider.',
            'dibuat_oleh' => 'provider',
            'status_negosiasi' => 'ditawar_ulang',
        ]);

        $nego->update([
            'harga_tawaran' => $hargaTawaran,
            'status_negosiasi' => 'ditawar_ulang',
        ]);

        return $newNego;
    }

    public function sendMessage(Provider $provider, int $id, string $pesan): Negosiasi
    {
        $nego = Negosiasi::where('id_provider', $provider->id_provider)
            ->where(function ($q) use ($id) {
                $q->where('id_negosiasi', $id)->orWhere('id_request', $id);
            })
            ->latest()
            ->first();

        if (!$nego) {
            $reqLayanan = RequestLayanan::findOrFail($id);
            return Negosiasi::create([
                'id_request' => $reqLayanan->id_request,
                'id_provider' => $provider->id_provider,
                'harga_tawaran' => $reqLayanan->harga_awal,
                'detail_negosiasi' => $pesan,
                'dibuat_oleh' => 'provider',
                'status_negosiasi' => 'pending',
            ]);
        }

        return Negosiasi::create([
            'id_request' => $nego->id_request,
            'id_provider' => $provider->id_provider,
            'harga_tawaran' => $nego->harga_tawaran,
            'detail_negosiasi' => $pesan,
            'dibuat_oleh' => 'provider',
            'status_negosiasi' => $nego->status_negosiasi,
        ]);
    }
}
