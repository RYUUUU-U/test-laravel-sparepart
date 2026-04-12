<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    /**
     * Handle an incoming request.
     * Pengganti: if(!isset($_SESSION['status']) || $_SESSION['status'] != 'login')
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('status') !== 'login') {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
