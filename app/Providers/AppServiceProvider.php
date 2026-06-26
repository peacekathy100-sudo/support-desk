<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\ExternalClient;
use App\Observers\ExternalClientObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Services\TicketService::class, function ($app) {
            return new \App\Services\TicketService(
                $app->make(\App\Services\NotificationService::class),
                $app->make(\App\Services\AuditService::class)
            );
        });
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Register observers
        ExternalClient::observe(ExternalClientObserver::class);
    }
}
