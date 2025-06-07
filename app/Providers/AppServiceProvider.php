<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Gate;
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
        /**
         * Rejestracja obserwatora
         *
         * https://laravel.com/docs/eloquent#observers
         */
        User::observe(UserObserver::class);

        /**
         * Autoryzacja dostępu do Pulse
         *
         * https://laravel.com/docs/pulse#dashboard-authorization
         */
        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });
    }
}
