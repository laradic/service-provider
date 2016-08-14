<?php
namespace Laradic\ServiceProvider\Plugins;

use Illuminate\Contracts\Foundation\Application;

/**
 * This is the class Config.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait Config
{
    protected $configPluginPriority = [ 10, 10 ];

    protected $configStrategy = 'defaultConfigStrategy';

    /**
     * bootConfigPlugin method
     *
     * @param Application $app
     */
    protected function startConfigPlugin($app)
    {
        $this->requiresPlugins(Resources::class, Paths::class);
        $this->onRegister('config', function ($app) {
            $this->registerConfigFiles();
        });
        $this->onBoot('config', function ($app) {
            $this->bootConfigFiles();
        });
    }

    /**
     * Adds the config files defined in $configFiles to the publish procedure.
     * Can be overriden to adjust default functionality
     */
    protected function bootConfigFiles($configFiles = null, $path = null)
    {
        if ( $configFiles === null ) {
            $configFiles = $this->configFiles;
        }
        if ( !is_array($configFiles) ) {
            $configFiles = [ $configFiles ];
        }
        if ( null !== $this->getRootDir() && null !== $configFiles ) {
            foreach ( $configFiles as $fileName ) {
                $filePath = $path === null ? $this->resolvePath('configPath') : path_join($path, $fileName);
                $this->publishes([ $filePath => config_path($fileName . '.php') ], 'config');
            }
        }
    }

    /**
     * The default config merge function, instead of using the laravel mergeConfigRom it
     *
     * @param $path
     * @param $key
     */
    protected function defaultConfigStrategy($path, $key)
    {
        $config = $this->app->make('config')->get($key, [ ]);
        $this->app->make('config')->set($key, array_replace_recursive(require $path, $config));
    }

    /**
     * Merges all defined config files defined in $configFiles.
     * Can be overriden to adjust default functionality
     */
    protected function registerConfigFiles($configFiles = null, $path = null)
    {
        if ( $configFiles === null ) {
            $configFiles = $this->configFiles;
        }
        if ( !is_array($configFiles) ) {
            $configFiles = [ $configFiles ];
        }
        if ( null !== $this->getRootDir() && null !== $configFiles ) {
            $path = $path ?: $this->resolvePath('configPath');
            foreach ( $configFiles as $key ) {
                call_user_func_array([ $this, $this->configStrategy ], [ path_join($path, $key . '.php'), $key ]);
            }
        }
    }

    /**
     * overrideConfig method
     *
     * @param        $fileName
     * @param string $method
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function overrideConfig($fileName, $method = 'array_replace_recursive')
    {
        /** @var \Illuminate\Config\Repository $config */
        $config    = $this->app->make('config');
        $fileName  = str_ensure_right($fileName, '.php');
        $filePath  = path_join($this->resolvePath('configPath'), $fileName);
        $overrides = $this->app[ 'fs' ]->getRequire($filePath);

        foreach ( $overrides as $k => $v ) {
            if ( $config->has($k) && is_array($this->app[ 'config' ]->get($k)) ) {
                $v = call_user_func($method, $config->get($k, [ ]), $v);
            }
            $config->set($k, $v);
        }
    }

}