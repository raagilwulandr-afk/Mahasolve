<?php

namespace App\Policies;

use App\Models\RequestLayanan;
use App\Models\User;

class RequestLayananPolicy
{
    public function view(User $user, RequestLayanan $requestLayanan): bool
    {
        return (int) $requestLayanan->id_user === (int) $user->id_user;
    }

    public function update(User $user, RequestLayanan $requestLayanan): bool
    {
        return (int) $requestLayanan->id_user === (int) $user->id_user && $requestLayanan->status_request === 'open';
    }

    public function delete(User $user, RequestLayanan $requestLayanan): bool
    {
        return (int) $requestLayanan->id_user === (int) $user->id_user && $requestLayanan->status_request === 'open';
    }
}
