<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Nocleg;
use App\Models\Rating;
use App\Models\Category;
use App\Models\Attraction;
use App\Models\BannedWord;
use App\Policies\UserPolicy;
use App\Policies\AdminPolicy;
use App\Policies\NoclegPolicy;
use App\Policies\RatingPolicy;
use App\Observers\UserObserver;
use App\Policies\CategoryPolicy;
use App\Policies\AttractionPolicy;
use App\Policies\BannedWordPolicy;
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
        
        Gate::define('admin-access', [AdminPolicy::class, 'adminAccess']);
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
        Gate::define('admin-access', [AdminPolicy::class, 'adminAccess']);
        Gate::policy(User::class, UserPolicy::class);
        //Gate::policy(Attraction::class, AttractionPolicy::class);
        //Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Nocleg::class, NoclegPolicy::class);
        //Gate::policy(Rating::class, RatingPolicy::class);
        Gate::policy(BannedWord::class, BannedWordPolicy::class);
    }
}