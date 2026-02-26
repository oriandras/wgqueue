<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Az alkalmazás konfigurálása és inicializálása.
 * Itt határozzuk meg az útvonalakat, a middleware-eket és a kivételkezelést.
 */
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // A webes middleware csoport kiegészítése az utolsó aktivitást frissítő osztállyal
        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastSeen::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Egyedi hibajelentési logika definiálása
        $exceptions->reportable(function (\Throwable $e) {
            // Ha konzolból fut az alkalmazás, vagy nincs adatbázis kapcsolat, ne naplózzunk az adatbázisba
            if (app()->runningInConsole() || !app()->bound('db')) {
                return;
            }

            try {
                // A hiba adatainak mentése a 'sys_errors' táblába
                // Közvetlen DB query-t használunk a Facade root hibák elkerülése érdekében a hiba jelentésekor
                \Illuminate\Support\Facades\DB::table('sys_errors')->insert([
                    'user_id' => auth()->id(),
                    'message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString(),
                    'url' => request()->fullUrl(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $fallbackException) {
                // Ha a naplózás közben is hiba történik (pl. hiányzó tábla), némán kezeljük,
                // hogy ne írjuk felül az eredeti hibaüzenetet egy újabbal.
            }
        });
    })->create();
