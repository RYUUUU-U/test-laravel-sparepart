<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'checkLogin'     => \App\Http\Middleware\CheckLogin::class,
            'checkRole'      => \App\Http\Middleware\CheckRole::class,
            'checkCustomer'  => \App\Http\Middleware\CheckCustomer::class,
        ]);

        // Exclude Xendit webhook dari CSRF verification
        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
            'api/courier/delivered/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
