<?php

namespace Eav\Api;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Eav\Api\Http\Controllers';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->mapApiRoutes($router);
    }

    /**
    * Define the "api" routes for the application.
    *
    * These routes are typically stateless.
    *
    * @return void
    */
    private function mapApiRoutes(Router $router)
    {
        $router->group([
            'middleware' => config('eav.api.middleware', 'web'),
            'prefix' => 'api/eav',
            'as' => 'api.eav.'
        ], function ($router) {
            $this->registerApiRoutes($router);
        });
    }


    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function registerApiRoutes(Router $router)
    {
        $router->group([
            'namespace' => $this->namespace
        ], function ($router) {
            require __DIR__.'/../../routes/api.php';
        });
    }
}
