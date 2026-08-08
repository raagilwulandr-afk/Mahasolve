<?php

namespace App\DataTransferObjects;

use App\Models\Negosiasi;

class ChatMessageData
{
    public function __construct(
        public int $id,
        public string $message,
        public float|int $hargaTawaran,
        public string $time,
        public string $sender,
        public bool $isProvider
    ) {}

    public static function fromModel(Negosiasi $chat, string $viewerRole = 'provider'): self
    {
        $isProviderSender = ($chat->dibuat_oleh === 'provider');
        $formattedMessage = $chat->detail_negosiasi 
            ?? ('Penawaran harga: Rp ' . number_format($chat->harga_tawaran, 0, ',', '.'));

        return new self(
            id: $chat->id_negosiasi,
            message: $formattedMessage,
            hargaTawaran: $chat->harga_tawaran,
            time: $chat->created_at ? $chat->created_at->format('H:i') : now()->format('H:i'),
            sender: $isProviderSender ? 'provider' : 'mahasiswa',
            isProvider: $isProviderSender
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'pesan' => $this->message,
            'text' => $this->message,
            'message' => $this->message,
            'harga_tawaran' => $this->hargaTawaran,
            'offered_price' => $this->hargaTawaran,
            'time' => $this->time,
            'sender' => $this->sender,
            'isProvider' => $this->isProvider,
        ];
    }
}
