<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderChat;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua order milik Provider yang sedang login
        $orders = Order::with(['customer', 'service', 'chats.sender'])
            ->where('provider_id', auth()->id())
            ->latest()
            ->get();

        // Order yang sedang aktif dipilah
        $activeOrderId = $request->get('active', $orders->first()?->id);
        $activeOrder = $orders->firstWhere('id', $activeOrderId);

        // UBAH BARIS INI: Pointing ke provider.order
        return view('provider.order', compact('orders', 'activeOrder'));
    }

    // Provider mengirim pesan balasan biasa
    public function sendMessage(Request $request, $orderId)
    {
        $request->validate(['pesan' => 'required|string']);

        OrderChat::create([
            'order_id'  => $orderId,
            'sender_id' => auth()->id(),
            'pesan'     => $request->pesan,
        ]);

        return back()->with('success', 'Pesan terkirim.');
    }

    // Provider mengajukan NEGO BALIK
    public function counterNego(Request $request, $orderId)
    {
        $request->validate([
            'harga_tawaran' => 'required|numeric|min:1000',
            'pesan'         => 'nullable|string',
        ]);

        $order = Order::where('provider_id', auth()->id())->findOrFail($orderId);

        // Update harga tawaran terkini pada order
        $order->update(['harga_tawaran' => $request->harga_tawaran]);

        // Simpan ke riwayat chat
        OrderChat::create([
            'order_id'      => $order->id,
            'sender_id'     => auth()->id(),
            'pesan'         => $request->pesan ?? 'Saya mengajukan penawaran harga baru.',
            'harga_tawaran' => $request->harga_tawaran,
        ]);

        return back()->with('success', 'Penawaran balik berhasil dikirim.');
    }

    // Provider TERIMA Negosiasi
    public function acceptNego($orderId)
    {
        $order = Order::where('provider_id', auth()->id())->findOrFail($orderId);
        $order->update(['status' => 'Diproses']);

        OrderChat::create([
            'order_id'  => $order->id,
            'sender_id' => auth()->id(),
            'pesan'     => '✓ Negosiasi disetujui! Pesanan langsung diproses.',
        ]);

        return back()->with('success', 'Negosiasi disetujui.');
    }

    // Provider TOLAK Negosiasi
    public function rejectNego($orderId)
    {
        $order = Order::where('provider_id', auth()->id())->findOrFail($orderId);
        $order->update(['status' => 'Ditolak']);

        OrderChat::create([
            'order_id'  => $order->id,
            'sender_id' => auth()->id(),
            'pesan'     => '✕ Mohon maaf, penawaran harga ditolak.',
        ]);

        return back()->with('success', 'Negosiasi ditolak.');
    }
}
