<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SentryContextServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!class_exists(\Sentry\State\HubInterface::class) || !app()->bound(\Sentry\State\HubInterface::class)) {
            return;
        }

        \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
            $request = request();
            $scope->setTag('app_env', app()->environment());
            $scope->setContext('request', [
                'path' => $request->path(),
                'method' => $request->method(),
                'correlation_id' => $request->attributes->get('correlation_id'),
            ]);
        });
    }
}
