<?php

namespace App\Http\Controllers;

use App\Models\Negosiasi;
use App\Models\Provider;
use App\Models\RequestLayanan;
use App\Services\NegotiationService;
use Illuminate\Http\Request;

class NegosiasiController extends Controller
{
    public function __construct(
        protected NegotiationService $negotiationService
    ) {}

    public function store(Request $request, RequestLayanan $requestLayanan, Provider $provider)
    {
        abort_unless((int) $requestLayanan->id_user === (int) auth()->user()->id_user, 403);
        abort_unless($requestLayanan->status_request === 'open', 400, 'Request ini sudah diproses.');

        $validated = $request->validate([
            'harga_tawaran' => 'required|numeric|min:0',
            'detail_negosiasi' => 'nullable|string',
        ]);

        $negosiasi = Negosiasi::firstOrCreate([
            'id_request' => $requestLayanan->id_request,
            'id_provider' => $provider->id_provider,
        ], [
            'harga_tawaran' => $validated['harga_tawaran'],
            'detail_negosiasi' => $validated['detail_negosiasi'] ?? null,
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);

        $negosiasi->update([
            'harga_tawaran' => $validated['harga_tawaran'],
            'status_negosiasi' => 'pending',
        ]);

        \App\Models\PesanNegosiasi::create([
            'id_negosiasi' => $negosiasi->id_negosiasi,
            'id_pengirim' => auth()->user()->id_user,
            'peran_pengirim' => 'mahasiswa',
            'pesan' => $validated['detail_negosiasi'] ?? ('Mengajukan penawaran harga Rp ' . number_format($validated['harga_tawaran'], 0, ',', '.')),
            'harga_tawaran' => $validated['harga_tawaran'],
        ]);

        $requestLayanan->update(['status_request' => 'diproses']);

        return redirect()
            ->route('negosiasi.show', $negosiasi->id_negosiasi)
            ->with('success', 'Permintaan berhasil diajukan ke provider, menunggu respon.');
    }

    public function show(Negosiasi $negosiasi)
    {
        $negosiasi->load('request.mahasiswa', 'provider.user', 'pesanan', 'pesanNegosiasi');
        $this->authorizeParticipant($negosiasi);

        $thread = $negosiasi->pesanNegosiasi;
        $terakhir = $negosiasi;

        $layananTerkait = $negosiasi->provider->layanan()
            ->where('kategori', $negosiasi->request->kategori)
            ->orderBy('harga')
            ->first() ?? $negosiasi->provider->layanan()->orderBy('harga')->first();

        return view('negosiasi.show', compact('negosiasi', 'thread', 'terakhir', 'layananTerkait'));
    }

    public function counterOffer(Request $request, Negosiasi $negosiasi)
    {
        $this->authorizeParticipant($negosiasi);

        abort_if(is_object($negosiasi->status_negosiasi) ? $negosiasi->status_negosiasi->value === 'disepakati' : $negosiasi->status_negosiasi === 'disepakati', 400, 'Negosiasi ini sudah disepakati.');

        $validated = $request->validate([
            'harga_tawaran' => 'required|numeric|min:0',
            'detail_negosiasi' => 'nullable|string',
        ]);

        $negosiasi->update([
            'harga_tawaran' => $validated['harga_tawaran'],
            'status_negosiasi' => 'pending',
        ]);

        \App\Models\PesanNegosiasi::create([
            'id_negosiasi' => $negosiasi->id_negosiasi,
            'id_pengirim' => auth()->user()->id_user,
            'peran_pengirim' => 'mahasiswa',
            'pesan' => $validated['detail_negosiasi'] ?? ('Menawarkan balik harga Rp ' . number_format($validated['harga_tawaran'], 0, ',', '.')),
            'harga_tawaran' => $validated['harga_tawaran'],
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('negosiasi.show', $negosiasi->id_negosiasi);
    }

    public function accept(Negosiasi $negosiasi)
    {
        $this->authorizeParticipant($negosiasi);
        $pesanan = $this->negotiationService->acceptNegotiation($negosiasi);

        return redirect()
            ->route('pesanan.show', ['pesanan' => $pesanan->id_pesanan, 'pay' => 1])
            ->with('success', 'Negosiasi disetujui! Silakan lanjutkan ke pembayaran.');
    }

    public function reject(Negosiasi $negosiasi)
    {
        $this->authorizeParticipant($negosiasi);

        $terakhir = Negosiasi::where('id_request', $negosiasi->id_request)
            ->where('id_provider', $negosiasi->id_provider)
            ->latest('created_at')
            ->first();

        $terakhir->update(['status_negosiasi' => 'ditolak']);
        $negosiasi->request->update(['status_request' => 'open']);

        return redirect()
            ->route('mahasiswa.request.show', $negosiasi->id_request)
            ->with('success', 'Negosiasi dibatalkan. Kamu bisa memilih provider lain.');
    }

    private function authorizeParticipant(Negosiasi $negosiasi): void
    {
        $user = auth()->user();
        $isMahasiswaOwner = (int) $negosiasi->request->id_user === (int) $user->id_user;
        $isProviderOwner = $user->provider && (int) $negosiasi->id_provider === (int) $user->provider->id_provider;

        abort_unless($isMahasiswaOwner || $isProviderOwner, 403);
    }
}
