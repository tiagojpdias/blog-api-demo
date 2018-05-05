<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->modelBinder();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::group([
            'middleware' => 'api',
        ], function ($router) {
            require base_path('routes/api/auth.php');
            require base_path('routes/api/posts.php');
            require base_path('routes/api/users.php');
        });
    }

    /**
     * Model binder.
     *
     * @throws NotFoundHttpException
     *
     * @return void
     */
    protected function modelBinder(): void
    {
        $models = [
            'post' => Post::class,
            'user' => User::class,
        ];

        foreach ($models as $key => $fqcn) {
            Route::bind($key, function ($id) use ($fqcn) {
                if (!$model = $fqcn::find($id)) {
                    throw new NotFoundHttpException(sprintf('%s Not Found', class_basename($fqcn)));
                }

                return $model;
            });
        }
    }
}
