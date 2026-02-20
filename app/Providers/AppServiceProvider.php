<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Offer;
use App\Models\Payment;
use App\Policies\BookingPolicy;
use App\Policies\OfferPolicy;
use App\Policies\PaymentPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Offer::class, OfferPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email', 'guest');
            $ip = $request->ip() ?? 'unknown';
            return [
                Limit::perMinute(5)->by(strtolower($email).'|'.$ip),
            ];
        });

        Queue::failing(function (JobFailed $event): void {
            Log::critical('queue.job_failed', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job_name' => $event->job->resolveName(),
                'exception' => $event->exception->getMessage(),
            ]);
        });

        if (class_exists(\Sentry\Laravel\Integration::class)) {
            \Sentry\Laravel\Integration::handles($this->app);
        }
    }
}
