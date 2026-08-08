<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\DetailPekerjaan;
use App\Models\Negosiasi;
use App\Models\Pesanan;
use App\Models\Provider;
use App\Models\TrackingPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $provider = Provider::where('id_user', $user->id_user)->first();

        if (!$provider) {
            $provider = Provider::create([
                'id_user' => $user->id_user,
                'rating' => 0.0,
                'detail_provider' => 'Provider Jasa Mahasolve',
            ]);
        }

        // Auto-initialize Negosiasi HANYA jika Provider mengklik request spesifik dari Dashboard (?active=id_request)
        $activeId = $request->get('active');
        if ($activeId) {
            $reqLayanan = \App\Models\RequestLayanan::find($activeId);
            if ($reqLayanan) {
                Negosiasi::firstOrCreate([
                    'id_request' => $reqLayanan->id_request,
                    'id_provider' => $provider->id_provider,
                ], [
                    'harga_tawaran' => $reqLayanan->harga_awal,
                    'detail_negosiasi' => 'Halo! Saya penyedia jasa terverifikasi dan siap mengambil pekerjaan ini.',
                    'dibuat_oleh' => 'provider',
                    'status_negosiasi' => 'pending',
                ]);
            }
        }

        // Ambil semua negosiasi/order milik Provider ini
        $negosiasiList = Negosiasi::with(['request.mahasiswa', 'pesanan.trackingPesanan', 'pesanan.detailPekerjaan'])
            ->where('id_provider', $provider->id_provider)
            ->latest()
            ->get();

        // Map ke format dummy/order view agar compatible dengan view provider.order
        $orders = $negosiasiList->map(function ($nego) use ($user) {
            $req = $nego->request;
            $mhs = $req ? $req->mahasiswa : null;
            $pesanan = $nego->pesanan;

            $statusText = match ($nego->status_negosiasi) {
                'disepakati' => $pesanan ? ucfirst($pesanan->status_pesanan) : 'Diproses',
                'ditolak' => 'Ditolak',
                default => 'Negosiasi',
            };

            return (object) [
                'id' => $nego->id_negosiasi,
                'raw_id' => $nego->id_negosiasi,
                'id_request' => $nego->id_request,
                'code' => $pesanan ? 'ORD-' . str_pad($pesanan->id_pesanan, 4, '0', STR_PAD_LEFT) : 'NEG-' . str_pad($nego->id_negosiasi, 4, '0', STR_PAD_LEFT),
                'created_at' => $nego->created_at ?? now(),
                'date' => $nego->created_at ? $nego->created_at->format('d M Y') : now()->format('d M Y'),
                'title' => $req->detail_kebutuhan ?? 'Request Jasa',
                'customerName' => $mhs->username ?? 'Mahasiswa',
                'category' => $req->kategori ?? 'Umum',
                'currentPrice' => $req->harga_awal ?? $nego->harga_tawaran,
                'customerOffer' => $nego->harga_tawaran,
                'description' => $req->detail_kebutuhan ?? 'Tidak ada catatan tambahan.',
                'avatarBg' => 'bg-indigo-600',
                'customer' => (object) [
                    'name' => $mhs->username ?? 'Mahasiswa',
                    'email' => $mhs->email ?? 'mahasiswa@student.ac.id',
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($mhs->username ?? 'M'),
                ],
                'service' => (object) [
                    'nama_layanan' => $req->detail_kebutuhan ?? 'Request Jasa',
                    'kategori' => $req->kategori ?? 'Umum',
                    'title' => $req->detail_kebutuhan ?? 'Request Jasa',
                    'category' => $req->kategori ?? 'Umum',
                    'price' => $req->harga_awal ?? $nego->harga_tawaran,
                ],
                'status' => $statusText,
                'harga_tawaran' => $nego->harga_tawaran,
                'harga_awal' => $req->harga_awal ?? $nego->harga_tawaran,
                'negotiation_price' => $nego->harga_tawaran,
                'notes' => $req->detail_kebutuhan ?? 'Tidak ada catatan tambahan.',
                'deskripsi_kebutuhan' => $req->detail_kebutuhan ?? 'Tidak ada deskripsi',
                'chats' => Negosiasi::where('id_request', $nego->id_request)
                    ->where('id_provider', $nego->id_provider)
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function ($chat) {
                        $isProviderSender = ($chat->dibuat_oleh === 'provider');
                        return (object) [
                            'id' => $chat->id_negosiasi,
                            'pesan' => $chat->detail_negosiasi ?? ('Penawaran harga: Rp ' . number_format($chat->harga_tawaran, 0, ',', '.')),
                            'text' => $chat->detail_negosiasi ?? ('Penawaran harga: Rp ' . number_format($chat->harga_tawaran, 0, ',', '.')),
                            'message' => $chat->detail_negosiasi ?? ('Penawaran harga: Rp ' . number_format($chat->harga_tawaran, 0, ',', '.')),
                            'harga_tawaran' => $chat->harga_tawaran,
                            'offered_price' => $chat->harga_tawaran,
                            'created_at' => $chat->created_at ?? now(),
                            'time' => $chat->created_at ? $chat->created_at->format('H:i') : now()->format('H:i'),
                            'isProvider' => $isProviderSender,
                            'sender' => $isProviderSender ? 'provider' : 'customer',
                        ];
                    }),
            ];
        });

        $activeOrderId = $request->get('active');
        $activeOrder = null;
        if ($activeOrderId) {
            $activeOrder = $orders->firstWhere('id', (int) $activeOrderId)
                ?? $orders->firstWhere('id_request', (int) $activeOrderId);
        }
        if (!$activeOrder) {
            $activeOrder = $orders->first();
        }

        return view('provider.order', compact('orders', 'activeOrder'));
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate(['pesan' => 'required|string']);
        $user = Auth::user();
        $provider = Provider::where('id_user', $user->id_user)->first();

        $nego = Negosiasi::where('id_provider', $provider->id_provider)
            ->where(function ($q) use ($id) {
                $q->where('id_negosiasi', $id)->orWhere('id_request', $id);
            })
            ->latest()
            ->first();

        if (!$nego) {
            $reqLayanan = \App\Models\RequestLayanan::findOrFail($id);
            $nego = Negosiasi::create([
                'id_request' => $reqLayanan->id_request,
                'id_provider' => $provider->id_provider,
                'harga_tawaran' => $reqLayanan->harga_awal,
                'detail_negosiasi' => $request->pesan,
                'dibuat_oleh' => 'provider',
                'status_negosiasi' => 'pending',
            ]);
        } else {
            Negosiasi::create([
                'id_request' => $nego->id_request,
                'id_provider' => $provider->id_provider,
                'harga_tawaran' => $nego->harga_tawaran,
                'detail_negosiasi' => $request->pesan,
                'dibuat_oleh' => 'provider',
                'status_negosiasi' => $nego->status_negosiasi,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'harga_tawaran' => $nego->harga_tawaran]);
        }

        return back();
    }

    public function counterNego(Request $request, $id)
    {
        $request->validate([
            'harga_tawaran' => 'required|numeric|min:1000',
            'pesan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $provider = Provider::where('id_user', $user->id_user)->first();

        $nego = Negosiasi::where('id_provider', $provider->id_provider)
            ->where(function ($q) use ($id) {
                $q->where('id_negosiasi', $id)->orWhere('id_request', $id);
            })
            ->latest()
            ->first();

        if (!$nego) {
            $reqLayanan = \App\Models\RequestLayanan::findOrFail($id);
            $nego = Negosiasi::create([
                'id_request' => $reqLayanan->id_request,
                'id_provider' => $provider->id_provider,
                'harga_tawaran' => $request->harga_tawaran,
                'detail_negosiasi' => $request->pesan ?? 'Penawaran balik dari provider.',
                'dibuat_oleh' => 'provider',
                'status_negosiasi' => 'ditawar_ulang',
            ]);
        } else {
            Negosiasi::create([
                'id_request' => $nego->id_request,
                'id_provider' => $provider->id_provider,
                'harga_tawaran' => $request->harga_tawaran,
                'detail_negosiasi' => $request->pesan ?? 'Penawaran balik dari provider.',
                'dibuat_oleh' => 'provider',
                'status_negosiasi' => 'ditawar_ulang',
            ]);

            $nego->update([
                'harga_tawaran' => $request->harga_tawaran,
                'status_negosiasi' => 'ditawar_ulang',
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'harga_tawaran' => $request->harga_tawaran]);
        }

        return redirect()->route('order', ['active' => $nego->id_negosiasi])->with('success', 'Penawaran balik berhasil dikirim.');
    }

    public function acceptNego($id)
    {
        $user = Auth::user();
        $provider = Provider::where('id_user', $user->id_user)->first();

        if (!$provider) {
            $provider = Provider::create([
                'id_user' => $user->id_user,
                'rating' => 0.0,
                'detail_provider' => 'Provider Jasa Mahasolve',
            ]);
        }

        $nego = Negosiasi::where('id_provider', $provider->id_provider)
            ->where(function ($q) use ($id) {
                $q->where('id_negosiasi', $id)->orWhere('id_request', $id);
            })
            ->latest()
            ->first();

        if (!$nego) {
            $reqLayanan = \App\Models\RequestLayanan::findOrFail($id);
            $nego = Negosiasi::create([
                'id_request' => $reqLayanan->id_request,
                'id_provider' => $provider->id_provider,
                'harga_tawaran' => $reqLayanan->harga_awal,
                'detail_negosiasi' => 'Provider menyetujui permintaan jasa sesuai budget mahasiswa.',
                'dibuat_oleh' => 'provider',
                'status_negosiasi' => 'disepakati',
            ]);
        } else {
            $nego->update(['status_negosiasi' => 'disepakati']);
        }

        if (!$nego->pesanan) {
            $pesanan = Pesanan::create([
                'id_negosiasi' => $nego->id_negosiasi,
                'harga_final' => $nego->harga_tawaran,
                'tanggal_pesanan' => now(),
                'status_pesanan' => 'menunggu_pengerjaan',
            ]);

            TrackingPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'status_pengerjaan' => 'Negosiasi disepakati. Pesanan dalam antrean pengerjaan.',
            ]);
        }

        return redirect()->route('order', ['active' => $nego->id_negosiasi])->with('success', 'Permintaan jasa berhasil diterima & disepakati! Pesanan telah dibuat.');
    }

    public function rejectNego($id)
    {
        $user = Auth::user();
        $provider = Provider::where('id_user', $user->id_user)->first();
        $nego = Negosiasi::where('id_provider', $provider->id_provider)->findOrFail($id);

        $nego->update(['status_negosiasi' => 'ditolak']);

        return back()->with('success', 'Negosiasi ditolak.');
    }

    // FASE 3: Update Progress & Deliverables Upload
    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'status_pesanan' => 'required|string',
            'pesan_progress' => 'nullable|string',
            'dokumen' => 'nullable|string',
            'file_dokumen' => 'nullable|file|max:10240',
        ]);

        $statusPesanan = ($request->status_pesanan === 'diproses' || $request->status_pesanan === 'dikerjakan') ? 'dikerjakan' : 'selesai';

        $user = Auth::user();
        $provider = Provider::where('id_user', $user->id_user)->firstOrFail();
        $nego = Negosiasi::where('id_provider', $provider->id_provider)->findOrFail($id);
        $pesanan = $nego->pesanan;

        if (!$pesanan) {
            $pesanan = Pesanan::create([
                'id_negosiasi' => $nego->id_negosiasi,
                'harga_final' => $nego->harga_tawaran,
                'tanggal_pesanan' => now(),
                'status_pesanan' => $statusPesanan,
            ]);
        } else {
            $pesanan->update(['status_pesanan' => $statusPesanan]);
        }

        $dokumenPath = $request->dokumen;

        if ($request->hasFile('file_dokumen')) {
            $uploadedFile = $request->file('file_dokumen');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $uploadedFile->getClientOriginalName());
            $uploadDir = public_path('uploads/deliverables');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $uploadedFile->move($uploadDir, $filename);
            $dokumenPath = asset('uploads/deliverables/' . $filename);
        }

        // Catat di TrackingPesanan
        TrackingPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'status_pengerjaan' => $request->pesan_progress ?? ("Status pesanan diperbarui menjadi: " . ucfirst($statusPesanan)),
            'file_progress' => $dokumenPath,
        ]);

        // Simpan deliverable jika ada berkas/link yang diserahkan
        if ($dokumenPath) {
            DetailPekerjaan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'dokumen' => $dokumenPath,
                'instruksi_pengerjaan' => $request->pesan_progress ?? 'Hasil pekerjaan telah diunggah oleh provider.',
                'format_hasil' => 'File / Link Hasil Pekerjaan',
                'tanggal_upload' => now(),
                'status' => 'lengkap',
            ]);
        }

        return back()->with('success', 'Progress pengerjaan & berkas deliverable berhasil diperbarui.');
    }

    public function cancelOrder(Request $request, $id)
    {
        $user = Auth::user();
        $provider = Provider::where('id_user', $user->id_user)->firstOrFail();
        $nego = Negosiasi::where('id_provider', $provider->id_provider)->findOrFail($id);

        if ($nego->pesanan) {
            $nego->pesanan->update(['status_pesanan' => 'dibatalkan']);
        }
        $nego->update(['status_negosiasi' => 'ditolak']);

        return back()->with('success', 'Pesanan berhasil dibatalkan oleh Mitra Provider.');
    }
}
