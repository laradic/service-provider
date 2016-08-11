<?php
namespace Laradic\ServiceProvider;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Laradic\Filesystem\Filesystem;
use Laradic\ServiceProvider\Exception\ProviderPluginDependencyException;
use ReflectionClass;

abstract class BaseServiceProvider extends LaravelServiceProvider
{
    // base

    protected $provides = [ ];

    /** @var \Laradic\Filesystem\Filesystem */
    protected $fs;


    // plugins

    private $started = false;

    private $registerCallbacks = [ ];

    private $bootCallbacks = [ ];

    private $providesCallbacks = [ ];


    // directories

    private $dir;

    private $rootDir;

    protected $scanDirs = true;

    protected $scanDirsMaxLevel = 4;


    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        parent::__construct($app);
        $this->startIfNotStarted();
        $this->fs = Filesystem::create();
    }


    private function startIfNotStarted()
    {
        if ( true === $this->started ) {
            return;
        }
        $this->started = true;
        $this->startPluginTraits();
    }

    private function startPluginTraits()
    {
        foreach ( $this->getPluginTraits() as $trait ) {
            if ( method_exists(get_called_class(), $method = 'start' . class_basename($trait) . 'Plugin') ) {
                call_user_func([ $this, $method ], $this->app);
            }
        }
    }

    private function getPluginTraits()
    {
        return array_values(class_uses_recursive(get_called_class()));
    }

    public function requiresPlugins()
    {
        $has     = class_uses_recursive(get_called_class());
        $check   = array_combine(func_get_args(), func_get_args());
        $missing = array_values(array_diff($check, $has));
        if ( isset($missing[ 0 ]) ) {
            $plugin = collect(debug_backtrace())->where('function', 'requiresPlugins')->first();
            throw ProviderPluginDependencyException::plugin($plugin[ 'file' ], implode(', ', $missing));
        }
    }

    private function resolveDirectories()
    {
        if ( $this->scanDirs !== true ) {
            return;
        }
        if ( $this->rootDir === null ) {
            $class     = new ReflectionClass(get_called_class());
            $filePath  = $class->getFileName();
            $this->dir = $rootDir = path_get_directory($filePath);
            $found     = false;
            for ( $i = 0; $i < $this->scanDirsMaxLevel; $i++ ) {
                if ( file_exists($composerPath = path_join($rootDir, 'composer.json')) ) {
                    $found = true;
                    break;
                } else {
                    $rootDir = path_get_directory($rootDir); // go 1 up
                }
            }
            if ( $found === false ) {
                throw new \OutOfBoundsException("Could not determinse composer.json file location in [{$this->dir}] or in {$this->scanDirsMaxLevel} parents of [$this->rootDir}]");
            }
            $this->rootDir = $rootDir;
        }

        $this->dir = $this->dir ?: path_join($this->rootDir, 'src');
    }

    public function addProvides($name, Closure $callback)
    {
        $this->providesCallbacks[] = compact('name', 'callback');
    }

    public function onRegister($name, $priority, Closure $callback)
    {
        $this->registerCallbacks[] = compact('name', 'priority', 'callback');
    }

    public function onBoot($name, $priority, Closure $callback)
    {
        $this->bootCallbacks[] = compact('name', 'priority', 'callback');
    }

    private function fireCallbacks($name, Closure $modifier = null, Closure $caller = null)
    {
        $list = collect($this->{$name . 'Callbacks'});
        if ( $modifier ) {
            $list = call_user_func_array($modifier, [ $list ]);
        }
        $caller = $caller ?: function (Closure $callback) {
            $callback->call($this, $this->app);
        };
        $list->pluck('callback')->each($caller);
    }

    /**
     * boot method
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
     * register method
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function register()
    {
        $app = $this->app;
        $this->resolveDirectories();

        $this->fireCallbacks('register', function (Collection $list) {
            return $list->sortBy('priority');
        });


        return $app;
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
            if ( is_array($result) ) {
                $provides = array_merge($provides, $result);
            }
        });
//
//        foreach ( $this->providers as $provider ) {
//            $instance = $this->app->resolveProviderClass($provider);
//
//            $provides = array_merge($provides, $instance->provides());
//        }
//
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


}