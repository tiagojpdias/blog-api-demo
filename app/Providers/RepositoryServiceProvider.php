<?php

namespace App\Providers;

use App\Repositories\PostEloquentRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserEloquentRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
        $repositories = [
            PostRepository::class => PostEloquentRepository::class,
            UserRepository::class => UserEloquentRepository::class,
        ];

        foreach ($repositories as $interface => $concrete) {
            $this->app->singleton($interface, function () use ($concrete) {
                return new $concrete();
            });
        }
    }
}
