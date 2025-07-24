<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\V2\ProfileService;
use App\Services\V2\MeetingService;
use App\Services\V2\ClubService;
use App\Repositories\V2\UserRepository;
use App\Repositories\V2\MeetingRepository;
use App\Repositories\V2\ClubRepository;

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
        $this->app->singleton(ClubRepository::class);
        
        // Регистрируем сервисы
        $this->app->singleton(ClubService::class, function ($app) {
            return new ClubService(
                $app->make(ClubRepository::class)
            );
        });
        
        $this->app->singleton(ProfileService::class, function ($app) {
            return new ProfileService(
                $app->make(UserRepository::class),
                $app->make(\App\Services\SubscriptionService::class),
                $app->make(ClubService::class)
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