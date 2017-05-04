<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2017. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2017 (c) Robin Radic
 * @license   https://laradic.mit-license.org The MIT License
 */

namespace Laradic\ServiceProvider\Plugins;

use Illuminate\Contracts\Foundation\Application;
use Laradic\ServiceProvider\BaseServiceProvider;

/**
 * This is the class Middleware.
 *
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 * @mixin BaseServiceProvider
 */
trait Routing
{
    /**
     * Collection of middleware.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * Collection of prepend middleware.
     *
     * @var array
     */
    protected $prependMiddleware = [];

    /**
     * Collection of route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    protected $middlewarePluginPriority = 30;

    /**
     * startMiddlewarePlugin method.
     *
     * @param Application $app
     */
    protected function startRoutingPlugin($app)
    {
        $this->requiresPlugins(Paths::class);
        $this->onRegister('routing', function (Application $app) {
            if (PHP_SAPI !== 'cli') {
                $router = $app->make('router');
                $kernel = $app->make('Illuminate\Contracts\Http\Kernel');

                foreach ($this->prependMiddleware as $middleware) {
                    $kernel->prependMiddleware($middleware);
                }

                foreach ($this->middleware as $middleware) {
                    $kernel->pushMiddleware($middleware);
                }

                foreach ($this->routeMiddleware as $key => $middleware) {
                    $router->middleware($key, $middleware);
                }
            }
        });

        $this->onBoot('routing', function (Application $app) {

            $this->routes;
        });
    }

    /**
     * Push a Middleware on to the stack.
     *
     * @param      $middleware
     *
     * @param bool $force
     *
     */
    protected function pushMiddleware($middleware, $force = false)
    {
        if (PHP_SAPI !== 'cli' && $force === false) {
            return $this->getHttpKernel();
        }

        $this->getHttpKernel()->pushMiddleware($middleware);
    }

    /**
     * getHttpKernel method.
     *
     * @return \Illuminate\Contracts\Http\Kernel|\Illuminate\Foundation\Http\Kernel
     */
    protected function getHttpKernel()
    {
        return $this->app->make('Illuminate\Contracts\Http\Kernel');
    }

    /**
     * getRouter method.
     *
     * @return \Illuminate\Contracts\Routing\Registrar|\Illuminate\Routing\Router
     */
    protected function getRouter()
    {
        return $this->app->make('router');
    }

    /**
     * Prepend a Middleware in the stack.
     *
     * @param $middleware
     *
     */
    protected function prependMiddleware($middleware, $force = false)
    {
        if (PHP_SAPI !== 'cli' && $force === false) {
            $this->getHttpKernel();
        }

        $this->getHttpKernel()->prependMiddleware($middleware);
    }

    /**
     * Add a route middleware. Will not be added when running in console.
     *
     * @param      $key
     * @param null $middleware
     * @param bool $force
     *
     * @return \Illuminate\Contracts\Routing\Registrar|\Illuminate\Routing\Router
     */
    protected function routeMiddleware($key, $middleware = null, $force = false)
    {
        if (PHP_SAPI === 'cli' && $force === false) {
            return $this->getRouter();
        }
        if (is_array($key)) {
            foreach ($key as $k => $m) {
                $this->routeMiddleware($k, $m);
            }
        }
        $this->getRouter()->middleware($key, $middleware);
    }
}