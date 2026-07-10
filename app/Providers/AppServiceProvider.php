<?php

namespace App\Providers;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Default password policy for Password::default() (used in registration
        // and password reset): min 8 chars, mixed case, and at least one number.
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers());

        // The app is styled with Bootstrap 5, not Tailwind.
        Paginator::useBootstrapFive();

        // Audit trail: record sign-ins and sign-outs.
        Event::listen(Login::class, fn (Login $event) => AuditLog::record('auth.login', 'Signed in', $event->user));
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                AuditLog::record('auth.logout', 'Signed out', $event->user);
            }
        });
    }
}
