<?php

namespace App\Policies;

use App\Models\Negosiasi;
use App\Models\User;

class NegosiasiPolicy
{
    public function view(User $user, Negosiasi $negosiasi): bool
    {
        $isMahasiswaOwner = (int) $negosiasi->request?->id_user === (int) $user->id_user;
        $isProviderOwner = $user->provider && (int) $negosiasi->id_provider === (int) $user->provider->id_provider;

        return $isMahasiswaOwner || $isProviderOwner;
    }

    public function accept(User $user, Negosiasi $negosiasi): bool
    {
        return $this->view($user, $negosiasi);
    }

    public function counter(User $user, Negosiasi $negosiasi): bool
    {
        return $this->view($user, $negosiasi);
    }

    public function reject(User $user, Negosiasi $negosiasi): bool
    {
        return $this->view($user, $negosiasi);
    }
}
