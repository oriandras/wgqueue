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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            // Ha terminálból futunk (pl. php artisan migrate), ne naplózzunk az adatbázisba
            if (app()->runningInConsole()) {
                return;
            }

            // Csak akkor próbáljunk menteni, ha a tábla már létezik
            if (\Illuminate\Support\Facades\Schema::hasTable('sys_errors')) {
                try {
                    \Illuminate\Support\Facades\DB::table('sys_errors')->insert([
                        'user_id' => auth()->id(),
                        'message' => $e->getMessage(),
                        'stack_trace' => $e->getTraceAsString(),
                        'url' => request()->fullUrl(),
                        'created_at' => now(),
                    ]);
                } catch (\Exception $fallbackException) {
                    // Ha mégis hiba történne a mentésnél, ne akassza meg a rendszert
                }
            }
        });
    })->create();
