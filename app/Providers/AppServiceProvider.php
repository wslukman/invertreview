<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // WAJIB DIIMPORT

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
        /**
         * Super Admin Bypass
         * Fungsi ini akan memeriksa apakah user memiliki role 'super_admin'.
         * Jika iya, maka semua pengecekan @can atau $this->authorize akan dianggap TRUE.
         */
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}