<?php

namespace App\Providers;

use App\Services\Payments\PaymentGatewayInterface;
use App\Services\Payments\RobokassaGateway;
use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Регистрируем PaymentService как синглтон
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });

        // Регистрируем RobokassaGateway и связываем его с интерфейсом
        $this->app->singleton(PaymentGatewayInterface::class, function ($app) {
            return new RobokassaGateway($app->make(PaymentService::class));
        });

        // Также можно биндить конкретную реализацию, если нужно
        $this->app->singleton(RobokassaGateway::class, function ($app) {
            return new RobokassaGateway($app->make(PaymentService::class));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 