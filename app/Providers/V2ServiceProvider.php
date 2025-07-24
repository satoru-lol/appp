<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\V2\ProfileService;
use App\Services\V2\MeetingService;
use App\Repositories\V2\UserRepository;
use App\Repositories\V2\MeetingRepository;

class V2ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Регистрируем репозитории
        $this->app->singleton(UserRepository::class);
        $this->app->singleton(MeetingRepository::class);
        
        // Регистрируем сервисы
        $this->app->singleton(ProfileService::class, function ($app) {
            return new ProfileService(
                $app->make(UserRepository::class),
                $app->make(\App\Services\SubscriptionService::class)
            );
        });
        
        $this->app->singleton(MeetingService::class, function ($app) {
            return new MeetingService(
                $app->make(MeetingRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}