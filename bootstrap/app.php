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
        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastSeen::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            // 1. Ha konzolból futunk, vagy nincs DB kapcsolat, azonnal álljunk le
            if (app()->runningInConsole() || !app()->bound('db')) {
                return;
            }

            try {
                // 2. NE használjunk Facade-ot (Schema::), mert az okozza a crasht!
                // Inkább csak próbáljuk meg elmenteni, és ha nem sikerül, a catch elkapja.
                \Illuminate\Support\Facades\DB::table('sys_errors')->insert([
                    'user_id' => auth()->id(),
                    'message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString(),
                    'url' => request()->fullUrl(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $fallbackException) {
                // Ha bármi hiba van (pl. nincs meg a tábla), némán maradjunk,
                // hogy ne okozzunk "Facade root" hibát az eredeti hiba helyett.
            }
        });
    })->create();
