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
        $user = auth()->user();
        $filterStatus = $httpRequest->query('status', 'semua');

        $semuaNegosiasi = Negosiasi::whereHas('request', fn ($q) => $q->where('id_user', $user->id_user))
            ->whereIn('status_negosiasi', ['pending', 'ditawar_ulang'])
            ->whereDoesntHave('pesanan')
            ->with('provider.user', 'request')
            ->get();

        $negosiasiList = $semuaNegosiasi->map(fn ($n) => (object) [
            'is_pesanan' => false,
            'id_pesanan' => null,
            'id_negosiasi' => $n->id_negosiasi,
            'status_pesanan' => 'negosiasi',
            'judul' => $n->request->detail_kebutuhan,
            'nama_provider' => $n->provider->user->username,
            'kode' => 'NEG-' . str_pad($n->id_negosiasi, 4, '0', STR_PAD_LEFT),
            'badge' => 'Negosiasi',
            'badge_color' => 'amber',
            'tanggal' => $n->created_at,
            'url' => route('pesanan.index', ['negosiasi' => $n->id_negosiasi, 'status' => $filterStatus]),
            'chat_url' => route('negosiasi.show', $n->id_negosiasi),
            'harga_tawaran' => $n->harga_tawaran ?? 0,
            'pesan_terakhir' => $n->detail_negosiasi ?? 'Penawaran sedang didiskusikan',
        ]);

        $semuaPesanan = Pesanan::whereHas('negosiasi.request', fn ($q) => $q->where('id_user', $user->id_user))
            ->with('negosiasi.provider.user', 'negosiasi.request', 'pembayaran', 'ratingReview')
            ->get();

        $pesananList = $semuaPesanan->map(fn ($p) => (object) [
            'is_pesanan' => true,
            'id_pesanan' => $p->id_pesanan,
            'id_negosiasi' => $p->id_negosiasi,
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
            'chat_url' => route('pesanan.show', $p->id_pesanan),
        ]);

        // Gabung semua (negosiasi aktif + pesanan), urut terbaru
        $gabungan = $negosiasiList->concat($pesananList)->sortByDesc('tanggal')->values();

        // Filter berdasarkan tab status jika dipilih
        if ($filterStatus === 'negosiasi') {
            $daftarAktivitas = $gabungan->filter(fn ($item) => ! $item->is_pesanan)->values();
        } elseif ($filterStatus === 'diproses') {
            $daftarAktivitas = $gabungan->filter(fn ($item) => $item->is_pesanan && in_array($item->status_pesanan, ['diproses', 'dikerjakan', 'revisi', 'menunggu_pengerjaan']))->values();
        } elseif ($filterStatus === 'selesai') {
            $daftarAktivitas = $gabungan->filter(fn ($item) => $item->is_pesanan && $item->status_pesanan === 'selesai')->values();
        } elseif ($filterStatus === 'dibatalkan') {
            $daftarAktivitas = $gabungan->filter(fn ($item) => $item->is_pesanan && $item->status_pesanan === 'dibatalkan')->values();
        } else {
            $daftarAktivitas = $gabungan;
        }

        // Pesanan / Negosiasi yang sedang dipilih untuk ditampilkan di panel detail kanan
        $selectedNegosiasiId = $httpRequest->query('negosiasi');
        $selectedPesananId = $httpRequest->query('pesanan');

        $selectedNegosiasi = null;
        $selected = null;

        if ($selectedNegosiasiId) {
            $selectedNegosiasi = $semuaNegosiasi->firstWhere('id_negosiasi', (int) $selectedNegosiasiId);
        } elseif ($selectedPesananId) {
            $selected = $semuaPesanan->firstWhere('id_pesanan', (int) $selectedPesananId);
        } else {
            // Default select first activity item
            $firstItem = $daftarAktivitas->first();
            if ($firstItem) {
                if ($firstItem->is_pesanan) {
                    $selected = $semuaPesanan->firstWhere('id_pesanan', $firstItem->id_pesanan);
                } else {
                    $selectedNegosiasi = $semuaNegosiasi->firstWhere('id_negosiasi', $firstItem->id_negosiasi);
                }
            }
        }

        $selectedId = $selected ? $selected->id_pesanan : null;

        // Calculate accurate stepper step index (0: Dipesan, 1: Dikonfirmasi, 2: Diproses, 3: Selesai)
        $stepIndex = 0;
        if ($selected) {
            if ($selected->status_pesanan === 'selesai') {
                $stepIndex = 3;
            } elseif (in_array($selected->status_pesanan, ['dikerjakan', 'revisi', 'diproses'])) {
                $stepIndex = 2;
            } elseif ($selected->status_pesanan === 'menunggu_pengerjaan' || ($selected->pembayaran && $selected->pembayaran->status_bayar === 'dikonfirmasi')) {
                $stepIndex = 1;
            } else {
                $stepIndex = 0;
            }
        }

        return view('pesanan.index', compact('daftarAktivitas', 'selected', 'selectedNegosiasi', 'selectedId', 'selectedNegosiasiId', 'stepIndex', 'filterStatus'));
    }

    public function show($id)
    {
        $pesanan = Pesanan::find($id);
        if (!$pesanan) {
            $pesanan = Pesanan::where('id_negosiasi', $id)->first() ?? Pesanan::latest()->first();
        }

        if (!$pesanan) {
            return redirect()->route('pesanan.index')->with('error', 'Pesanan tidak ditemukan.');
        }

        $this->authorizeParticipant($pesanan);

        $pesanan->load(
            'negosiasi.request.mahasiswa',
            'negosiasi.provider.user',
            'detailPekerjaan',
            'trackingPesanan',
            'pembayaran',
            'ratingReview'
        );

        $chats = Negosiasi::where('id_request', $pesanan->negosiasi->id_request)
            ->where('id_provider', $pesanan->negosiasi->id_provider)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($chat) {
                $isMahasiswaSender = ($chat->dibuat_oleh === 'mahasiswa');
                return (object) [
                    'id' => $chat->id_negosiasi,
                    'message' => $chat->detail_negosiasi ?? ('Penawaran harga: Rp ' . number_format($chat->harga_tawaran, 0, ',', '.')),
                    'harga_tawaran' => $chat->harga_tawaran,
                    'offered_price' => $chat->harga_tawaran,
                    'time' => $chat->created_at ? $chat->created_at->format('H:i') : now()->format('H:i'),
                    'sender' => $isMahasiswaSender ? 'mahasiswa' : 'provider',
                ];
            });

        return view('pesanan.show', compact('pesanan', 'chats'));
    }

    // Struk sederhana (bisa diprint) untuk pesanan yang sudah selesai
    public function struk($id)
    {
        $pesanan = Pesanan::find($id) ?? Pesanan::where('id_negosiasi', $id)->first();
        if (!$pesanan) {
            return redirect()->route('pesanan.index');
        }
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
