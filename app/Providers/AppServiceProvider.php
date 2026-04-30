<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Rate Limiter: Checkout ─────────────────────────────────────────
        // Batasi 1 checkout per customer per 30 detik untuk mencegah double-submit.
        RateLimiter::for('checkout', function (Request $request) {
            $key = $request->session()->get('customer_id', $request->ip());

            return Limit::perSecond(1, 30) // 1 request per 30 detik
                ->by('checkout:' . $key)
                ->response(function () {
                    return back()->with(
                        'error',
                        'Anda baru saja melakukan checkout. Mohon tunggu 30 detik sebelum mencoba lagi.'
                    );
                });
        });
    }
}
