<?php

namespace App\Http\Controllers;

use App\Models\Negosiasi;
use App\Models\Pesanan;
use App\Models\Provider;
use App\Models\RequestLayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NegosiasiController extends Controller
{
    // PB-04: Mahasiswa mengajukan permintaan jasa ke provider terpilih dari katalog
    // -> jadi baris pertama di thread negosiasi (chat)
    public function store(Request $request, RequestLayanan $requestLayanan, Provider $provider)
    {
        abort_unless((int) $requestLayanan->id_user === (int) auth()->user()->id_user, 403);
        abort_unless($requestLayanan->status_request === 'open', 400, 'Request ini sudah diproses.');

        $validated = $request->validate([
            'harga_tawaran' => 'required|numeric|min:0',
            'detail_negosiasi' => 'nullable|string',
        ]);

        $negosiasi = Negosiasi::create([
            'id_request' => $requestLayanan->id_request,
            'id_provider' => $provider->id_provider,
            'harga_tawaran' => $validated['harga_tawaran'],
            'detail_negosiasi' => $validated['detail_negosiasi'] ?? null,
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);

        $requestLayanan->update(['status_request' => 'diproses']);

        return redirect()
            ->route('negosiasi.show', $negosiasi->id_negosiasi)
            ->with('success', 'Permintaan berhasil diajukan ke provider, menunggu respon.');
    }

    // Halaman chat: tampilkan seluruh riwayat tawaran (thread) request+provider yang sama
    public function show(Negosiasi $negosiasi)
    {
        $negosiasi->load('request.mahasiswa', 'provider.user', 'pesanan');
        $this->authorizeParticipant($negosiasi);

        $thread = Negosiasi::where('id_request', $negosiasi->id_request)
            ->where('id_provider', $negosiasi->id_provider)
            ->orderBy('created_at')
            ->get();

        $terakhir = $thread->last();

        // Layanan representatif provider ini (untuk kartu "Detail Layanan" di sidebar)
        $layananTerkait = $negosiasi->provider->layanan()
            ->where('kategori', $negosiasi->request->kategori)
            ->orderBy('harga')
            ->first() ?? $negosiasi->provider->layanan()->orderBy('harga')->first();

        return view('negosiasi.show', compact('negosiasi', 'thread', 'terakhir', 'layananTerkait'));
    }

    // PB-05: Mahasiswa mengirim pesan/tawaran baru (jadi baris baru di thread)
    public function counterOffer(Request $request, Negosiasi $negosiasi)
    {
        $this->authorizeParticipant($negosiasi);

        $terakhir = Negosiasi::where('id_request', $negosiasi->id_request)
            ->where('id_provider', $negosiasi->id_provider)
            ->latest('created_at')
            ->first();

        abort_if($terakhir->status_negosiasi === 'disepakati', 400, 'Negosiasi ini sudah disepakati.');

        $validated = $request->validate([
            'harga_tawaran' => 'required|numeric|min:0',
            'detail_negosiasi' => 'nullable|string',
        ]);

        // Tawaran lama yang masih pending otomatis dianggap "ditawar ulang"
        if ($terakhir->status_negosiasi === 'pending') {
            $terakhir->update(['status_negosiasi' => 'ditawar_ulang']);
        }

        Negosiasi::create([
            'id_request' => $negosiasi->id_request,
            'id_provider' => $negosiasi->id_provider,
            'harga_tawaran' => $validated['harga_tawaran'],
            'detail_negosiasi' => $validated['detail_negosiasi'] ?? null,
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);

        return redirect()->route('negosiasi.show', $negosiasi->id_negosiasi)->with('success', 'Pesan terkirim.');
    }

    // Setuju dengan tawaran terakhir di thread -> otomatis buat pesanan (PB-05 -> PB-06)
    public function accept(Negosiasi $negosiasi)
    {
        $this->authorizeParticipant($negosiasi);

        $terakhir = Negosiasi::where('id_request', $negosiasi->id_request)
            ->where('id_provider', $negosiasi->id_provider)
            ->latest('created_at')
            ->first();

        abort_if($terakhir->status_negosiasi === 'disepakati', 400, 'Sudah disepakati sebelumnya.');

        DB::transaction(function () use ($terakhir, $negosiasi) {
            $terakhir->update(['status_negosiasi' => 'disepakati']);

            $pesanan = Pesanan::create([
                'id_negosiasi' => $terakhir->id_negosiasi,
                'harga_final' => $terakhir->harga_tawaran,
                'tanggal_pesanan' => now(),
                'status_pesanan' => 'menunggu_pengerjaan',
            ]);

            $negosiasi->request->update(['status_request' => 'diproses']);
        });

        return redirect()
            ->route('pesanan.show', $terakhir->pesanan->id_pesanan)
            ->with('success', 'Kesepakatan tercapai! Silakan lengkapi detail pekerjaan.');
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
