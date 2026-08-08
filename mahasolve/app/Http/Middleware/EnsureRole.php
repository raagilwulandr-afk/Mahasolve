<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Contoh pakai di route: ->middleware('role:mahasiswa') atau ->middleware('role:provider')
     * Bisa juga beberapa role sekaligus: ->middleware('role:mahasiswa,provider')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        $activeRole = $user->getActiveRole();

        // If target route requires provider role and active mode is provider, ensure provider profile exists
        if (in_array('provider', $roles, true) && $activeRole === 'provider') {
            $user->getOrCreateProvider();
        }

        abort_unless(in_array($activeRole, $roles, true), 403, 'Kamu tidak punya akses ke halaman ini. Silakan beralih mode di navigasi atas.');

        return $next($request);
    }
}
