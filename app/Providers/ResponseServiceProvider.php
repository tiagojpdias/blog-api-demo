<?php

namespace App\Providers;

use App\Support\ResponseFactory;
use Illuminate\Contracts\Routing\ResponseFactory as FactoryContract;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register custom ResponseFactory.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FactoryContract::class, function ($app) {
            return new ResponseFactory($app[ViewFactory::class], $app['redirect']);
        });
    }
}
