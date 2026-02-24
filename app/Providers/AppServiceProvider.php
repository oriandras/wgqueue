<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Példa: az a felhasználó admin, akinek az is_admin oszlopa true,
        // vagy akinek az ID-ja 1 (ezt alakítsd a saját rendszeredhez)
        Gate::define('admin', function ($user) {
            // return $user->id === 1;
            return $user->is_admin === 1;
        });
    }
}
