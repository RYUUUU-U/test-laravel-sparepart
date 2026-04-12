<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Pengganti: if($_SESSION['role'] != 'admin') { ... }
     * Usage in routes: middleware('checkRole:admin')
     *                  middleware('checkRole:admin,kasir')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = session('role');

        if (!$userRole || !in_array($userRole, $roles)) {
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda tidak punya izin untuk halaman ini.');
        }

        return $next($request);
    }
}
