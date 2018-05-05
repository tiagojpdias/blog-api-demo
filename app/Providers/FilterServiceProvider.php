<?php

namespace App\Providers;

use App\Filters\PostEloquentFilter;
use App\Filters\PostFilter;
use App\Filters\UserEloquentFilter;
use App\Filters\UserFilter;
use Illuminate\Support\ServiceProvider;

class FilterServiceProvider extends ServiceProvider
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
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $filters = [
            PostFilter::class => PostEloquentFilter::class,
            UserFilter::class => UserEloquentFilter::class,
        ];

        foreach ($filters as $interface => $concrete) {
            $this->app->singleton($interface, function () use ($concrete) {
                return new $concrete();
            });
        }
    }
}
