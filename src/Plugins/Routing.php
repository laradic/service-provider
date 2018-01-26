<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2018. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2018 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
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
    protected $middleware = [/** [ class ] */ ];

    /**
     * Collection of prepend middleware.
     *
     * @var array
     */
    protected $prependMiddleware = [/** [ class ] */ ];

    /**
     * Collection of route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [/** [ name => class ] */ ];

    /**
     * Collection of middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [/** [ group => [ middleware ] ] */ ];

    /**
     * Collection of prepend group middleware.
     *
     * @var array
     */
    protected $prependGroupMiddleware = [/** [ group => middleware ] */ ];

    /**
     * Collection of group middleware.
     *
     * @var array
     */
    protected $groupMiddleware = [/** [ group => middleware ] */ ];

    protected $routesPath = '{packagePath}/routes';

    protected $routeFiles = [];


    protected $routingPluginPriority = 30;

    /**
     * startMiddlewarePlugin method.
     *
     * @param Application $app
     */
    protected function startRoutingPlugin($app)
    {
        $this->requiresPlugins(Paths::class, Resources::class);
        $this->onRegister('routing', function (Application $app) {
            if (PHP_SAPI !== 'cli' || $this->app->runningUnitTests()) {
                $router = $app->make('router');
                $kernel = $app->make('Illuminate\Contracts\Http\Kernel');

                foreach ($this->prependMiddleware as $class) {
                    $kernel->prependMiddleware($class);
                }

                foreach ($this->middleware as $class) {
                    $kernel->pushMiddleware($class);
                }

                foreach ($this->routeMiddleware as $name => $class) {
                    if (method_exists($router, 'middleware')) {
                        $router->middleware($name, $class);
                    } else {
                        $router->aliasMiddleware($name, $class);
                    }
                }

                foreach ($this->middlewareGroups as $groupName => $classes) {
                    $router->middlewareGroup($groupName, $classes);
                }

                foreach ($this->prependGroupMiddleware as $group => $class) {
                    $router->prependMiddlewareToGroup($group, $class);
                }

                foreach ($this->groupMiddleware as $group => $class) {
                    $router->pushMiddlewareToGroup($group, $class);
                }
            }
        });

        $this->onBoot('routing', function (Application $app) {
            foreach ($this->routeFiles as $routeFile) {
                $this->loadRoutesFrom(path_join($this->resolvePath('routesPath'), str_ensure_right($routeFile, '.php')));
            }
        });

        static::refreshRoutes($app);
    }

    protected static $hasPushedRefreshCallback = false;

    protected function pushMiddlewareToGroup($group, $class)
    {
        $this->getRouter()->pushMiddlewareToGroup($group, $class);
    }

    /**
     * refreshRoutes method
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    private static function refreshRoutes(Application $app)
    {
        if (static::$hasPushedRefreshCallback) {
            return;
        }
        static::$hasPushedRefreshCallback = true;
        $app->booted(function (Application $app) {
            $routes = $app->make('router')->getRoutes();
            if (method_exists($routes, 'refreshNameLookups')) {
                $routes->refreshNameLookups();
            }
            if (method_exists($routes, 'refreshActionLookups')) {
                $routes->refreshActionLookups();
            }
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
        if (PHP_SAPI !== 'cli' && $force === false && $this->app->runningUnitTests() === false) {
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
        if (PHP_SAPI === 'cli' && $force === false && $this->app->runningUnitTests() === false) {
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
