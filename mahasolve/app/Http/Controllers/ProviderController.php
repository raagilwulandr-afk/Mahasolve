<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\RequestLayanan;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    // PB-02 & PB-03: Pencarian dan penyaringan penyedia jasa
    public function index(Request $request)
    {
        $providers = Provider::with(['user', 'layanan'])
            ->when($request->kategori, function ($query, $kategori) {
                $query->whereHas('layanan', fn ($q) => $q->where('kategori', $kategori));
            })
            ->orderByDesc('rating')
            ->get();

        $openRequests = RequestLayanan::where('status_request', 'open')->latest('id_request')->get();

        return view('providers.index', compact('providers', 'openRequests'));
    }

    public function show(Provider $provider)
    {
        $provider->load(['user', 'layanan']);

        return view('providers.show', compact('provider'));
    }
}
