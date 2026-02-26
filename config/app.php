<?php

/**
 * Alkalmazás szintű konfigurációk.
 */
return [

    /**
     * Alkalmazás neve.
     */
    'name' => env('APP_NAME', 'Laravel'),

    /**
     * Alkalmazás környezete (production/local).
     */
    'env' => env('APP_ENV', 'production'),

    /**
     * Hibakereső mód állapota.
     */
    'debug' => (bool) env('APP_DEBUG', false),

    /**
     * Az alkalmazás alapértelmezett URL címe.
     */
    'url' => env('APP_URL', 'http://localhost'),

    /**
     * Alkalmazás időzónája.
     */
    'timezone' => env('APP_TIMEZONE', 'Europe/Budapest'),

    /**
     * Alkalmazás nyelvi beállítása.
     */
    'locale' => env('APP_LOCALE', 'en'),

    /**
     * Tartalék nyelv, ha az elsődleges nem elérhető.
     */
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /**
     * Faker könyvtár nyelve.
     */
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /**
     * Titkosítási algoritmus és kulcs.
     */
    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    /**
     * Korábbi titkosítási kulcsok a rotációhoz.
     */
    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /**
     * Karbantartási mód beállításai.
     */
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
