<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

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
        Carbon::setLocale('id');

        // Event::listen(Login::class, function ($event) {
        //     log_activity($event->user, 'login', 'User login');
        // });

        // Event::listen(Logout::class, function ($event) {
        //     if ($event->user) {
        //         log_activity($event->user, 'logout', 'User logout');
        //     }
        // });
    }
}
