<?php

namespace App\DataTransferObjects;

use App\Models\Negosiasi;

class OrderViewData
{
    public static function fromNegotiation(Negosiasi $nego): array
    {
        $req = $nego->request;
        $mhs = $req->mahasiswa;
        $pesanan = $nego->pesanan;

        $statusText = match ($nego->status_negosiasi) {
            'disepakati' => $pesanan ? ucfirst(str_replace('_', ' ', $pesanan->status_pesanan)) : 'Menunggu Pengerjaan',
            'ditawar_ulang' => 'Penawaran Balik',
            'ditolak' => 'Ditolak',
            default => 'Pending',
        };

        $chats = $nego->pesanNegosiasi->map(function ($chat) use ($nego) {
            $isProviderSender = ($chat->peran_pengirim === 'provider');
            return [
                'id' => $chat->id_pesan,
                'pesan' => $chat->pesan,
                'text' => $chat->pesan,
                'message' => $chat->pesan,
                'harga_tawaran' => $chat->harga_tawaran ?? $nego->harga_tawaran,
                'offered_price' => $chat->harga_tawaran ?? $nego->harga_tawaran,
                'time' => $chat->created_at ? $chat->created_at->format('H:i') : now()->format('H:i'),
                'sender' => $chat->peran_pengirim,
                'isProvider' => $isProviderSender,
            ];
        });

        return [
            'id' => $nego->id_negosiasi,
            'raw_id' => $nego->id_negosiasi,
            'id_request' => $nego->id_request,
            'id_pesanan' => $pesanan?->id_pesanan,
            'customerName' => $mhs->username ?? 'Mahasiswa',
            'category' => $req->kategori ?? 'Umum',
            'currentPrice' => $req->harga_awal ?? $nego->harga_tawaran,
            'customerOffer' => $nego->harga_tawaran,
            'description' => $req->detail_kebutuhan ?? 'Tidak ada catatan tambahan.',
            'avatarBg' => 'bg-indigo-600',
            'customer' => [
                'name' => $mhs->username ?? 'Mahasiswa',
                'email' => $mhs->email ?? 'mahasiswa@student.ac.id',
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($mhs->username ?? 'M'),
            ],
            'service' => [
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
            'chats' => $chats,
        ];
    }
}
