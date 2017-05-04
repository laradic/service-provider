<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2017. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2017 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
 */

namespace Laradic\ServiceProvider;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Laradic\ServiceProvider\Exception\ProviderPluginDependencyException;
use ReflectionClass;

/**
 * This is the class BaseServiceProvider.
 *
 * @author  Robin Radic
 */
abstract class BaseServiceProvider extends LaravelServiceProvider
{
    // base

    /** @var array */
    protected $provides = [];

    /** @var \Laradic\Filesystem\Filesystem */
    protected $fs;

    // magic

    /** @var array */
    protected $getVariables = [];

    /** @var array */
    protected $callCallbacks = [];

    // plugins

    /** @var bool */
    private $started = false;

    /** @var array */
    private $registerCallbacks = [];

    /** @var array */
    private $bootCallbacks = [];

    /** @var array */
    private $providesCallbacks = [];

    // directories

    /** @var */
    private $dir;

    /** @var */
    private $rootDir;

    /** @var bool */
    protected $scanDirs = true;

    /** @var int */
    protected $scanDirsMaxLevel = 4;

    // extra event handlers

    /**
     * Declaring the method named here will make it so it will be called on application booting.
     *
     * @var string|null
     */
    protected $bootingMethod = 'booting';

    /**
     * Declaring the method named here will make it so it will be called when the application has booted.
     *
     * @var string|null
     */
    protected $bootedMethod = 'booted';

    /**
     * {@inheritdoc}
     */
    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        parent::__construct($app);
        $this->startIfNotStarted();
        $this->fs = new Filesystem();
    }

    /**
     * boot method.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function boot()
    {
        $app = $this->app;

        $this->fireCallbacks('boot', function (Collection $list) {
            return $list->sortBy('priority');
        });

        return $app;
    }

    /**
     * register method.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function register()
    {
        $app = $this->app;
        $this->resolveDirectories();

        if ($this->bootingMethod !== null && method_exists($this, $this->bootingMethod)) {
            $this->app->booting(function (Application $app) {
                $app->call([$this, $this->bootingMethod]);
            });
        }

        if ($this->bootedMethod !== null && method_exists($this, $this->bootedMethod)) {
            $this->app->booted(function (Application $app) {
                $app->call([$this, $this->bootedMethod]);
            });
        }

        $this->fireCallbacks('register', function (Collection $list) {
            return $list->sortBy('priority');
        });

        return $app;
    }

    /**
     * startIfNotStarted method.
     */
    private function startIfNotStarted()
    {
        if (true === $this->started) {
            return;
        }
        $this->started = true;
        $this->startPluginTraits();
    }

    /**
     * startPluginTraits method.
     */
    private function startPluginTraits()
    {
        foreach ($this->getPluginTraits() as $trait) {
            if (method_exists(get_called_class(), $method = 'start'.class_basename($trait).'Plugin')) {
                call_user_func([$this, $method], $this->app);
            }
        }
    }

    /**
     * getPluginTraits method.
     *
     * @return array
     */
    private function getPluginTraits()
    {
        return array_values(class_uses_recursive(get_called_class()));
    }

    /**
     * requiresPlugins method.
     */
    public function requiresPlugins()
    {
        $has = class_uses_recursive(get_called_class());
        $check = array_combine(func_get_args(), func_get_args());
        $missing = array_values(array_diff($check, $has));
        if (isset($missing[ 0 ])) {
            $plugin = collect(debug_backtrace())->where('function', 'requiresPlugins')->first();
            throw ProviderPluginDependencyException::plugin($plugin[ 'file' ], implode(', ', $missing));
        }
    }

    /**
     * resolveDirectories method.
     */
    private function resolveDirectories()
    {
        if ($this->scanDirs !== true) {
            return;
        }
        if ($this->rootDir === null) {
            $class = new ReflectionClass(get_called_class());
            $filePath = $class->getFileName();
            $this->dir = $rootDir = path_get_directory($filePath);
            $found = false;
            for ($i = 0; $i < $this->scanDirsMaxLevel; ++$i) {
                if (file_exists($composerPath = path_join($rootDir, 'composer.json'))) {
                    $found = true;
                    break;
                } else {
                    $rootDir = path_get_directory($rootDir); // go 1 up
                }
            }
            if ($found === false) {
                throw new \OutOfBoundsException("Could not determinse composer.json file location in [{$this->dir}] or in {$this->scanDirsMaxLevel} parents of [$this->rootDir}]");
            }
            $this->rootDir = $rootDir;
        }

        $this->dir = $this->dir ?: path_join($this->rootDir, 'src');
    }

    /**
     * addProvides method.
     *
     * @param          $name
     * @param \Closure $callback
     */
    public function addProvides($name, Closure $callback)
    {
        $this->providesCallbacks[] = compact('name', 'callback');
    }

    /**
     * getPluginPriority method.
     *
     * @param     $name
     * @param int $index If a plugin priority is defined as array, the 0 index is for register and 1 for boot
     *
     * @return int|mixed
     */
    private function getPluginPriority($name, $index = 0)
    {
        $priority = 10;
        if (property_exists($this, "{$name}PluginPriority")) {
            $value = $this->{$name.'PluginPriority'};
            $priority = is_array($value) ? $value[ $index ] : $value;
        }

        return $priority;
    }

    /**
     * onRegister method.
     *
     * @param          $name
     * @param \Closure $callback
     */
    public function onRegister($name, Closure $callback)
    {
        $priority = $this->getPluginPriority($name);
        $this->registerCallbacks[] = compact('name', 'priority', 'callback');
    }

    /**
     * onBoot method.
     *
     * @param          $name
     * @param \Closure $callback
     */
    public function onBoot($name, Closure $callback)
    {
        $priority = $this->getPluginPriority($name, 1);
        $this->bootCallbacks[] = compact('name', 'priority', 'callback');
    }

    /**
     * fireCallbacks method.
     *
     * @param               $name
     * @param \Closure|null $modifier
     * @param \Closure|null $caller
     */
    private function fireCallbacks($name, Closure $modifier = null, Closure $caller = null)
    {
        $list = collect($this->{$name.'Callbacks'});
        if ($modifier) {
            $list = call_user_func_array($modifier, [$list]);
        }
        $caller = $caller ?: function (Closure $callback) {
            $callback->bindTo($this);
            $callback($this->app);
        };
        $list->pluck('callback')->each($caller);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        $provides = $this->provides;

        $this->fireCallbacks('provides', null, function (Closure $callback) use (&$provides) {
            $result = $callback->call($this, $this->app, $provides);
            if (is_array($result)) {
                $provides = array_merge($provides, $result);
            }
        });

//        foreach ( $this->providers as $provider ) {
//            $instance = $this->app->resolveProviderClass($provider);

//            $provides = array_merge($provides, $instance->provides());
//        }

//        $commands = [ ];
//        foreach ( $this->commands as $k => $v ) {
//            if ( is_string($k) ) {
//                $commands[] = $k;
//            }
//        }

//        return array_merge(
//            $provides,
//            array_keys($this->aliases),
//            array_keys($this->bindings),
//            array_keys($this->share),
//            array_keys($this->shared),
//            array_keys($this->singletons),
//            array_keys($this->weaklings),
//            $commands
//        );
        return $provides;
    }

    /**
     * @return string|null
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * __call method.
     *
     * @param       $method
     * @param array $params
     *
     * @return mixed
     */
    public function __call($method, $params = [])
    {
        if ($this->callCallbacks[ $method ]) {
            return call_user_func_array($method, $params);
        }
        throw new \BadMethodCallException("Method [{$method}] not found");
    }

    /**
     * __get method.
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->getVariables[ $name ])) {
            $var = $this->getVariables[ $name ];
            if ($var instanceof Closure) {
                return $var($this);
            }
        }
    }
}
