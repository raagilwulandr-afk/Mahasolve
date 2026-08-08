<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        $allowed = collect($roles)
            ->map(fn ($role) => strtolower($role))
            ->contains(strtolower($user->role));

        if (!$allowed) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return $next($request);
    }
}