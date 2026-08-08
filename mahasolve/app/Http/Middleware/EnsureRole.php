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
        abort_unless($request->user() && in_array($request->user()->role, $roles, true), 403, 'Kamu tidak punya akses ke halaman ini.');

        return $next($request);
    }
}
