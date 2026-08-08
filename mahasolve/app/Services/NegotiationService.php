<?php

namespace App\Services;

use App\Models\Negosiasi;
use App\Models\PesanNegosiasi;
use App\Models\Pesanan;
use App\Models\Provider;
use App\Models\RequestLayanan;
use App\Models\TrackingPesanan;
use Illuminate\Support\Facades\DB;

class NegotiationService
{
    public function acceptNegotiation(Negosiasi $negosiasi): Pesanan
    {
        abort_if(is_object($negosiasi->status_negosiasi) ? $negosiasi->status_negosiasi->value === 'disepakati' : $negosiasi->status_negosiasi === 'disepakati', 400, 'Sudah disepakati sebelumnya.');

        $pesanan = null;

        DB::transaction(function () use ($negosiasi, &$pesanan) {
            $negosiasi->update(['status_negosiasi' => 'disepakati']);

            $pesanan = Pesanan::create([
                'id_negosiasi' => $negosiasi->id_negosiasi,
                'harga_final' => $negosiasi->harga_tawaran,
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
        $nego = Negosiasi::firstOrCreate([
            'id_request' => $id,
            'id_provider' => $provider->id_provider,
        ], [
            'harga_tawaran' => $hargaTawaran,
            'detail_negosiasi' => $pesan ?? 'Penawaran balik dari provider.',
            'dibuat_oleh' => 'provider',
            'status_negosiasi' => 'ditawar_ulang',
        ]);

        $nego->update([
            'harga_tawaran' => $hargaTawaran,
            'status_negosiasi' => 'ditawar_ulang',
        ]);

        PesanNegosiasi::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'id_pengirim' => $provider->id_user,
            'peran_pengirim' => 'provider',
            'pesan' => $pesan ?? ('Menawarkan harga baru: Rp ' . number_format($hargaTawaran, 0, ',', '.')),
            'harga_tawaran' => $hargaTawaran,
        ]);

        return $nego;
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
            $nego = Negosiasi::create([
                'id_request' => $reqLayanan->id_request,
                'id_provider' => $provider->id_provider,
                'harga_tawaran' => $reqLayanan->harga_awal,
                'detail_negosiasi' => $pesan,
                'dibuat_oleh' => 'provider',
                'status_negosiasi' => 'pending',
            ]);
        }

        PesanNegosiasi::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'id_pengirim' => $provider->id_user,
            'peran_pengirim' => 'provider',
            'pesan' => $pesan,
            'harga_tawaran' => $nego->harga_tawaran,
        ]);

        return $nego;
    }
}

