<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCustomer
{
    /**
     * Pastikan pengguna adalah pelanggan yang sudah login.
     *
     * Menggunakan session manual (session key: customer_id) agar tidak
     * bertabrakan dengan session admin yang menggunakan key: id_user, role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('customer_id')) {
            return redirect()->route('customer.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
