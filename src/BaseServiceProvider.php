<?php
namespace Laradic\ServiceProvider;

use Closure;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use ReflectionClass;

abstract class BaseServiceProvider extends LaravelServiceProvider
{
    // plugins

    private $started = false;

    private $registerCallbacks = [ ];

    private $bootCallbacks = [ ];

    private $providesCallbacks = [];


    // directories

    private $dir;

    private $rootDir;

    protected $scanDirs;

    protected $scanDirsMaxLevel;


    const ON_REGISTER = 1;
    const ON_REGISTERED = 2;
    const ON_BOOT = 3;
    const ON_BOOTED = 4;

    const METHOD_REGISTER = 1;
    const METHOD_RESOLVE = 2;
    // base

    protected $provides = [];


    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        parent::__construct($app);
        $this->startIfNotStarted();
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
        return class_uses_recursive(get_called_class());
    }

    protected function requiresPlugins(){
        $plugins = func_get_args();
        $diff = array_diff($this->getPluginTraits(), $plugins);
        $a = 'a';
    }

    private function resolveDirectories()
    {
        if ( $this->scanDirs !== true ) {
            return;
        }
        if ( $this->rootDir === null ) {
            $class    = new ReflectionClass(get_called_class());
            $filePath = $class->getFileName();
            $this->dir = $rootDir  = path_get_directory($filePath);
            $found    = false;
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

    protected function addProvides($name, Closure $handler)
    {
        $this->providesCallbacks[] = compact('name', 'handler');
    }

    protected function onRegister($name, $priority, Closure $handler)
    {
        $this->registerCallbacks[] = compact('name', 'priority', 'handler');
    }

    protected function onBoot($name, $priority, Closure $handler)
    {
        $this->bootCallbacks[] = compact('name', 'priority', 'handler');
    }

    public function boot()
    {
        $app = $this->app;

        collect($this->bootCallbacks)
            ->sortBy('priority')
            ->pluck('handler')
            ->each(function (\Closure $handler) {
                $handler->call($this, $this->app);
            });


        return $app;
    }

    public function register()
    {
        $app = $this->app;
        $this->resolveDirectories();

        collect($this->registerCallbacks)
            ->sortBy('priority')
            ->pluck('handler')
            ->each(function (\Closure $handler) {
                $handler->call($this, $this->app);
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

        collect($this->providesCallbacks)
            ->pluck('handler')
            ->each(function (\Closure $handler) use (&$provides) {
                $result = $handler->call($this, $this->app, $provides);
                if(is_array($result)){
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
     * @return mixed
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }


}