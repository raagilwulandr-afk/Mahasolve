<?php

namespace App\Policies;

use App\Models\Pesanan;
use App\Models\User;

class PesananPolicy
{
    public function view(User $user, Pesanan $pesanan): bool
    {
        $isMahasiswaOwner = (int) $pesanan->negosiasi?->request?->id_user === (int) $user->id_user;
        $isProviderOwner = $user->provider && (int) $pesanan->negosiasi?->id_provider === (int) $user->provider->id_provider;

        return $isMahasiswaOwner || $isProviderOwner;
    }

    public function pay(User $user, Pesanan $pesanan): bool
    {
        return $user->isMahasiswa() && (int) $pesanan->negosiasi?->request?->id_user === (int) $user->id_user;
    }

    public function review(User $user, Pesanan $pesanan): bool
    {
        return $user->isMahasiswa() 
            && (int) $pesanan->negosiasi?->request?->id_user === (int) $user->id_user
            && $pesanan->bolehDireview();
    }
}
