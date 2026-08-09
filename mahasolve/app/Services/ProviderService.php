<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Provider;
use Illuminate\Support\Facades\Cache;

class ProviderService
{
    public function getDashboardStats(Provider $provider): object
    {
        $cacheKey = "provider_stats_{$provider->id_provider}";

        return Cache::remember($cacheKey, 10, function () use ($provider) {
            $totalPendapatan = (int) Pesanan::whereHas('negosiasi', fn ($q) => $q->where('id_provider', $provider->id_provider))
                ->where('status_pesanan', 'selesai')
                ->sum('harga_final');

            $pesananAktif = Pesanan::whereHas('negosiasi', fn ($q) => $q->where('id_provider', $provider->id_provider))
                ->whereIn('status_pesanan', ['menunggu_pengerjaan', 'dikerjakan', 'revisi'])
                ->count();

            $pesananSelesai = Pesanan::whereHas('negosiasi', fn ($q) => $q->where('id_provider', $provider->id_provider))
                ->where('status_pesanan', 'selesai')
                ->count();

            $totalPenarikan = (int) $provider->penarikanSaldo()
                ->whereIn('status', ['diproses', 'disetujui'])
                ->sum('jumlah');

            $saldoBisaDitarik = max(0, $totalPendapatan - $totalPenarikan);

            return (object) [
                'totalPendapatan' => $totalPendapatan,
                'pesananAktif' => $pesananAktif,
                'pesananSelesai' => $pesananSelesai,
                'saldoBisaDitarik' => $saldoBisaDitarik,
            ];
        });
    }

    public function clearStatsCache(int $providerId): void
    {
        Cache::forget("provider_stats_{$providerId}");
    }
}
