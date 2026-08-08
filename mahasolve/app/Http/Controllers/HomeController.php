<?php

namespace App\Http\Controllers;

use App\Models\Negosiasi;
use App\Models\Pesanan;

class HomeController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return view('welcome');
        }

        $user = auth()->user();

        // Negosiasi yang masih berjalan & belum jadi pesanan
        $negosiasiAktif = Negosiasi::whereHas('request', fn ($q) => $q->where('id_user', $user->id_user))
            ->whereIn('status_negosiasi', ['pending', 'ditawar_ulang'])
            ->whereDoesntHave('pesanan')
            ->with('provider.user', 'request')
            ->get()
            ->map(fn ($n) => (object) [
                'judul' => $n->request->detail_kebutuhan,
                'nama_lawan' => $n->provider->user->username,
                'kode' => 'NEG-' . str_pad($n->id_negosiasi, 4, '0', STR_PAD_LEFT),
                'badge' => 'Negosiasi',
                'badge_color' => 'amber',
                'tanggal' => $n->created_at,
                'url' => route('negosiasi.show', $n->id_negosiasi),
            ]);

        // Pesanan yang masih berjalan (belum selesai/dibatalkan)
        $pesananAktif = Pesanan::whereHas('negosiasi.request', fn ($q) => $q->where('id_user', $user->id_user))
            ->whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])
            ->with('negosiasi.provider.user', 'negosiasi.request')
            ->get()
            ->map(fn ($p) => (object) [
                'judul' => $p->negosiasi->request->detail_kebutuhan,
                'nama_lawan' => $p->negosiasi->provider->user->username,
                'kode' => 'ORD-' . str_pad($p->id_pesanan, 4, '0', STR_PAD_LEFT),
                'badge' => $p->status_pesanan === 'revisi' ? 'Revisi' : 'Diproses',
                'badge_color' => 'indigo',
                'tanggal' => $p->tanggal_pesanan,
                'url' => route('pesanan.show', $p->id_pesanan),
            ]);

        $aktivitasAktif = $negosiasiAktif->concat($pesananAktif)
            ->sortByDesc('tanggal')
            ->take(4)
            ->values();

        // Riwayat pesanan yang sudah selesai
        $riwayat = Pesanan::whereHas('negosiasi.request', fn ($q) => $q->where('id_user', $user->id_user))
            ->where('status_pesanan', 'selesai')
            ->with('negosiasi.request')
            ->latest('tanggal_pesanan')
            ->take(4)
            ->get();

        return view('mahasiswa.dashboard', compact('aktivitasAktif', 'riwayat'));
    }
}
