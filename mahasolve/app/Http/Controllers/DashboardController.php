<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'provider') {
            $provider = $user->provider;
            $negosiasiMasuk = $provider
                ? $provider->negosiasi()->with('requestLayanan')->latest('id_negosiasi')->get()
                : collect();

            return view('dashboard', [
                'role' => 'provider',
                'provider' => $provider,
                'negosiasiMasuk' => $negosiasiMasuk,
                'layanan' => $provider ? $provider->layanan : collect(),
            ]);
        }

        $requestLayanan = $user->requestLayanan()->latest('id_request')->get();

        return view('dashboard', [
            'role' => 'mahasiswa',
            'requestLayanan' => $requestLayanan,
        ]);
    }
}
