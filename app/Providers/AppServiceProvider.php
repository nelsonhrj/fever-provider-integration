<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Repositories\EloquentEventRepository;
use App\Services\Contracts\ProviderSyncServiceInterface;
use App\Services\ProviderSyncService;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository binding
        $this->app->singleton(
            EventRepositoryInterface::class,
            EloquentEventRepository::class
        );

        $this->app->singleton(
            ProviderSyncServiceInterface::class,
            function ($app) {
                return new ProviderSyncService(
                    $app->make(Client::class),
                    $app->make(EventRepositoryInterface::class)
                );
            }
        );
    }
}
