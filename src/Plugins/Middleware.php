<?php
/**
 * Part of the CLI PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */

namespace Laradic\ServiceProvider\Plugins;

use Laradic\ServiceProvider\BaseServiceProvider;

/**
 * This is the class Middleware.
 *
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 * @mixin BaseServiceProvider
 */
trait Middleware
{
    /**
     * Collection of middleware.
     *
     * @var array
     */
    protected $middleware = [ ];

    /**
     * Collection of prepend middleware.
     *
     * @var array
     */
    protected $prependMiddleware = [ ];

    /**
     * Collection of route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [ ];

    protected function startMiddlewarePlugin($app)
    {
        $this->onRegister('middleware', 1, function ($app) {
            if ( !$app->runningInConsole() ) {
                $router = $app->make('router');
                $kernel = $app->make('Illuminate\Contracts\Http\Kernel');

                foreach ( $this->prependMiddleware as $middleware ) {
                    $kernel->prependMiddleware($middleware);
                }

                foreach ( $this->middleware as $middleware ) {
                    $kernel->pushMiddleware($middleware);
                }

                foreach ( $this->routeMiddleware as $key => $middleware ) {
                    $router->middleware($key, $middleware);
                }
            }

        });
    }

}