<?php

namespace App\Http\Controllers\Provider;

use App\DataTransferObjects\OrderViewData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CounterNegotiationRequest;
use App\Http\Requests\UpdateProgressRequest;
use App\Models\Negosiasi;
use App\Models\Provider;
use App\Models\RequestLayanan;
use App\Services\NegotiationService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        protected NegotiationService $negotiationService,
        protected OrderService $orderService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $provider = $user->getOrCreateProvider();

        $activeId = $request->get('active');
        if ($activeId) {
            $reqLayanan = RequestLayanan::find($activeId);
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

        $negosiasiList = Negosiasi::with(['request.mahasiswa', 'pesanan.trackingPesanan', 'pesanan.detailPekerjaan'])
            ->where('id_provider', $provider->id_provider)
            ->latest('id_negosiasi')
            ->get()
            ->unique('id_request')
            ->values();

        $orders = $negosiasiList->map(fn ($nego) => OrderViewData::fromNegotiation($nego));

        $activeOrder = null;
        if ($activeId) {
            $activeOrder = $orders->firstWhere('id', (int) $activeId)
                ?? $orders->firstWhere('id_request', (int) $activeId);
        }
        if (!$activeOrder) {
            $activeOrder = $orders->first();
        }

        return view('provider.order', compact('orders', 'activeOrder'));
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate(['pesan' => 'required|string']);
        $provider = Auth::user()->getOrCreateProvider();

        $nego = $this->negotiationService->sendMessage($provider, (int) $id, $request->pesan);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'harga_tawaran' => $nego->harga_tawaran]);
        }

        return back();
    }

    public function counterNego(CounterNegotiationRequest $request, $id)
    {
        $provider = Auth::user()->getOrCreateProvider();
        $nego = $this->negotiationService->counterOffer($provider, (int) $id, $request->harga_tawaran, $request->pesan);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'harga_tawaran' => $request->harga_tawaran]);
        }

        return redirect()->route('order', ['active' => $nego->id_negosiasi])
            ->with('success', 'Penawaran balik berhasil dikirim.');
    }

    public function acceptNego($id)
    {
        $provider = Auth::user()->getOrCreateProvider();

        $nego = Negosiasi::where('id_provider', $provider->id_provider)
            ->where(function ($q) use ($id) {
                $q->where('id_negosiasi', $id)->orWhere('id_request', $id);
            })
            ->latest()
            ->first();

        if (!$nego) {
            $reqLayanan = RequestLayanan::findOrFail($id);
            $nego = Negosiasi::create([
                'id_request' => $reqLayanan->id_request,
                'id_provider' => $provider->id_provider,
                'harga_tawaran' => $reqLayanan->harga_awal,
                'detail_negosiasi' => 'Provider menyetujui permintaan jasa sesuai budget mahasiswa.',
                'dibuat_oleh' => 'provider',
                'status_negosiasi' => 'disepakati',
            ]);
        }

        if (!$nego->pesanan) {
            $this->negotiationService->acceptNegotiation($nego);
        }

        return redirect()->route('order', ['active' => $nego->id_negosiasi])
            ->with('success', 'Permintaan jasa berhasil diterima & disepakati! Pesanan telah dibuat.');
    }

    public function rejectNego($id)
    {
        $provider = Auth::user()->getOrCreateProvider();
        $nego = Negosiasi::where('id_provider', $provider->id_provider)->findOrFail($id);

        $nego->update(['status_negosiasi' => 'ditolak']);

        return back()->with('success', 'Negosiasi ditolak.');
    }

    public function updateProgress(UpdateProgressRequest $request, $id)
    {
        $provider = Auth::user()->getOrCreateProvider();
        $nego = Negosiasi::where('id_provider', $provider->id_provider)->findOrFail($id);
        $pesanan = $nego->pesanan;

        if (!$pesanan) {
            $pesanan = $this->negotiationService->acceptNegotiation($nego);
        }

        $this->orderService->updateProgress(
            pesanan: $pesanan,
            statusInput: $request->status_pesanan,
            pesanProgress: $request->pesan_progress,
            dokumenPath: $request->dokumen,
            uploadedFile: $request->file('file_dokumen')
        );

        return back()->with('success', 'Progress pengerjaan & berkas deliverable berhasil diperbarui.');
    }

    public function cancelOrder(Request $request, $id)
    {
        $provider = Auth::user()->getOrCreateProvider();
        $nego = Negosiasi::where('id_provider', $provider->id_provider)->findOrFail($id);

        if ($nego->pesanan) {
            if ($nego->pesanan->status_pesanan === 'selesai') {
                return back()->with('error', 'Pesanan yang sudah selesai tidak dapat dibatalkan.');
            }
            $nego->pesanan->update(['status_pesanan' => 'dibatalkan']);
        }
        $nego->update(['status_negosiasi' => 'ditolak']);

        return back()->with('success', 'Pesanan berhasil dibatalkan oleh Mitra Provider.');
    }
}
