<?php

namespace App\Services;

use App\Enums\ServiceCategory;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CatalogService
{
    public function getCategories(): array
    {
        return Cache::remember('service_categories_formatted', 3600, function () {
            return ServiceCategory::allFormatted();
        });
    }

    public function searchProviders(string $kategoriAktif, ?string $searchTerm = null): Collection
    {
        return Provider::whereHas('layanan', fn ($q) => $q->where('kategori', $kategoriAktif))
            ->when($searchTerm, function ($q) use ($searchTerm) {
                $q->where(function ($qq) use ($searchTerm) {
                    $qq->whereHas('user', fn ($u) => $u->where('username', 'like', "%{$searchTerm}%"))
                       ->orWhereHas('layanan', fn ($l) => $l->where('nama_layanan', 'like', "%{$searchTerm}%"));
                });
            })
            ->with([
                'user:id_user,username,email',
                'layanan' => fn ($q) => $q->where('kategori', $kategoriAktif)->orderBy('harga'),
            ])
            ->withCount([
                'negosiasi as order_count' => fn ($q) => $q->whereHas('pesanan', fn ($p) => $p->where('status_pesanan', 'selesai')),
                'negosiasi as review_count' => fn ($q) => $q->whereHas('pesanan.ratingReview'),
            ])
            ->orderByDesc('rating')
            ->get()
            ->map(function ($provider) {
                $provider->harga_mulai = $provider->layanan->min('harga');
                return $provider;
            });
    }
}
