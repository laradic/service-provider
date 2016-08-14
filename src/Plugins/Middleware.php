<?php
/**
 * Part of the CLI PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */

namespace Laradic\ServiceProvider\Plugins;

use Illuminate\Contracts\Foundation\Application;
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

    protected $middlewarePluginPriority = 20;

    /**
     * startMiddlewarePlugin method
     *
     * @param Application $app
     */
    protected function startMiddlewarePlugin($app)
    {
        $this->onRegister('middleware', function ($app) {
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

    /**
     * Push a Middleware on to the stack
     *
     * @param $middleware
     *
     * @return mixed
     */
    protected function pushMiddleware($middleware, $force = false)
    {
        if ( $this->app->runningInConsole() && $force === false ) {
            return $this->getHttpKernel();
        }
        return $this->getHttpKernel()->pushMiddleware($middleware);
    }

    /**
     * getHttpKernel method
     *
     * @return \App\Http\Kernel|\Illuminate\Contracts\Http\Kernel
     */
    protected function getHttpKernel()
    {
        return $this->app->make('Illuminate\Contracts\Http\Kernel');
    }

    /**
     * getRouter method
     *
     * @return \Illuminate\Contracts\Routing\Registrar|\Illuminate\Routing\Router
     */
    protected function getRouter()
    {
        return $this->app->make('router');
    }

    /**
     * Prepend a Middleware in the stack
     *
     * @param $middleware
     *
     * @return \Illuminate\Contracts\Routing\Registrar|\Illuminate\Routing\Router
     */
    protected function prependMiddleware($middleware, $force = false)
    {
        if ( $this->app->runningInConsole() && $force === false ) {
            $this->getHttpKernel();
        }

        return $this->getHttpKernel()->prependMiddleware($middleware);
    }

    /**
     * Add a route middleware. Will not be added when running in console.
     *
     * @param      $key
     * @param null $middleware
     *
     * @param bool $force
     *
     * @return \Illuminate\Contracts\Routing\Registrar|\Illuminate\Routing\Router
     */
    protected function routeMiddleware($key, $middleware = null, $force = false)
    {

        if ( $this->app->runningInConsole() && $force === false ) {
            return $this->getRouter();
        }
        if ( is_array($key) ) {
            foreach ( $key as $k => $m ) {
                $this->routeMiddleware($k, $m);
            }
            return $this->getRouter();
        } else {
            $this->getRouter()->middleware($key, $middleware);
        }
    }
}