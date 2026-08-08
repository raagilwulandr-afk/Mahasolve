<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Provider;

class ProviderService
{
    public function getDashboardStats(Provider $provider): object
    {
        $totalPendapatan = Pesanan::whereHas('negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })
        ->where('status_pesanan', 'selesai')
        ->sum('harga_final') ?? 0;

        $pesananAktif = Pesanan::whereHas('negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })
        ->whereIn('status_pesanan', ['menunggu_pengerjaan', 'dikerjakan', 'revisi'])
        ->count();

        $pesananSelesai = Pesanan::whereHas('negosiasi', function ($q) use ($provider) {
            $q->where('id_provider', $provider->id_provider);
        })
        ->where('status_pesanan', 'selesai')
        ->count();

        return (object) [
            'totalPendapatan' => $totalPendapatan,
            'pesananAktif' => $pesananAktif,
            'pesananSelesai' => $pesananSelesai,
            'saldoBisaDitarik' => $totalPendapatan,
        ];
    }
}
