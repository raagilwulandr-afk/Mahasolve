<?php

namespace App\Http\Controllers;

use App\Models\Negosiasi;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    // Peta progres status_pesanan -> index step (dipakai stepper di halaman Order)
    public const STEP_MAP = [
        'menunggu_pengerjaan' => 0,
        'dikerjakan' => 2,
        'revisi' => 2,
        'selesai' => 3,
        'dibatalkan' => -1,
    ];

    // Halaman Order: master-detail berisi SEMUA aktivitas (negosiasi aktif + pesanan + riwayat)
    public function index(Request $httpRequest)
    {
        $user = auth()->user();
        $filterStatus = $httpRequest->query('status', 'semua');

        $negosiasiAktif = Negosiasi::whereHas('request', fn ($q) => $q->where('id_user', $user->id_user))
            ->whereIn('status_negosiasi', ['pending', 'ditawar_ulang'])
            ->whereDoesntHave('pesanan')
            ->with('provider.user', 'request')
            ->get()
            ->map(fn ($n) => (object) [
                'is_pesanan' => false,
                'id_pesanan' => null,
                'status_pesanan' => 'diproses',
                'judul' => $n->request->detail_kebutuhan,
                'nama_provider' => $n->provider->user->username,
                'kode' => 'NEG-' . str_pad($n->id_negosiasi, 4, '0', STR_PAD_LEFT),
                'badge' => 'Negosiasi',
                'badge_color' => 'amber',
                'tanggal' => $n->created_at,
                'url' => route('negosiasi.show', $n->id_negosiasi),
            ]);

        $semuaPesanan = Pesanan::whereHas('negosiasi.request', fn ($q) => $q->where('id_user', $user->id_user))
            ->with('negosiasi.provider.user', 'negosiasi.request', 'pembayaran', 'ratingReview')
            ->get();

        $pesananList = $semuaPesanan->map(fn ($p) => (object) [
            'is_pesanan' => true,
            'id_pesanan' => $p->id_pesanan,
            'status_pesanan' => $p->status_pesanan,
            'judul' => $p->negosiasi->request->detail_kebutuhan,
            'nama_provider' => $p->negosiasi->provider->user->username,
            'kode' => 'ORD-' . str_pad($p->id_pesanan, 4, '0', STR_PAD_LEFT),
            'badge' => match ($p->status_pesanan) {
                'selesai' => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
                'revisi' => 'Revisi',
                default => 'Diproses',
            },
            'badge_color' => match ($p->status_pesanan) {
                'selesai' => 'green',
                'dibatalkan' => 'gray',
                default => 'indigo',
            },
            'tanggal' => $p->tanggal_pesanan,
            'url' => route('pesanan.index', ['pesanan' => $p->id_pesanan, 'status' => $filterStatus]),
        ]);

        // Gabung semua (negosiasi aktif + pesanan), urut terbaru
        $gabungan = $negosiasiAktif->concat($pesananList)->sortByDesc('tanggal')->values();

        // Filter berdasarkan tab status jika dipilih
        if ($filterStatus === 'diproses') {
            $daftarAktivitas = $gabungan->filter(fn ($item) => in_array($item->status_pesanan, ['diproses', 'dikerjakan', 'revisi', 'menunggu_pengerjaan']))->values();
        } elseif ($filterStatus === 'selesai') {
            $daftarAktivitas = $gabungan->filter(fn ($item) => $item->status_pesanan === 'selesai')->values();
        } elseif ($filterStatus === 'dibatalkan') {
            $daftarAktivitas = $gabungan->filter(fn ($item) => $item->status_pesanan === 'dibatalkan')->values();
        } else {
            $daftarAktivitas = $gabungan;
        }

        // Calculate accurate stepper step index (0: Dipesan, 1: Dikonfirmasi, 2: Diproses, 3: Selesai)
        $stepIndex = 0;
        if ($selected) {
            if ($selected->status_pesanan === 'selesai') {
                $stepIndex = 3;
            } elseif (in_array($selected->status_pesanan, ['dikerjakan', 'revisi'])) {
                $stepIndex = 2;
            } elseif ($selected->pembayaran && $selected->pembayaran->status_bayar === 'dikonfirmasi') {
                $stepIndex = 1;
            } else {
                $stepIndex = 0;
            }
        }

        return view('pesanan.index', compact('daftarAktivitas', 'selected', 'selectedId', 'stepIndex', 'filterStatus'));
    }

    public function show(Pesanan $pesanan)
    {
        $this->authorizeParticipant($pesanan);

        $pesanan->load(
            'negosiasi.request.mahasiswa',
            'negosiasi.provider.user',
            'detailPekerjaan',
            'trackingPesanan',
            'pembayaran',
            'ratingReview'
        );

        return view('pesanan.show', compact('pesanan'));
    }

    // Struk sederhana (bisa diprint) untuk pesanan yang sudah selesai
    public function struk(Pesanan $pesanan)
    {
        $this->authorizeParticipant($pesanan);
        $pesanan->load('negosiasi.request', 'negosiasi.provider.user', 'pembayaran');

        return view('pesanan.struk', compact('pesanan'));
    }

    private function authorizeParticipant(Pesanan $pesanan): void
    {
        $user = auth()->user();
        $isMahasiswaOwner = (int) $pesanan->negosiasi->request->id_user === (int) $user->id_user;
        $isProviderOwner = $user->provider && (int) $pesanan->negosiasi->id_provider === (int) $user->provider->id_provider;

        abort_unless($isMahasiswaOwner || $isProviderOwner, 403);
    }
}
