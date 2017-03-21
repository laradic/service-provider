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

namespace Laradic\ServiceProvider\Plugins;

/**
 * This is the class Providers.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait Providers
{
    /**
     * These Service Providers will be registered. Basicaly providing a shortcut to app()->register(). Use FQN.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * These Service Providers will be registered as deferred. Basicaly providing a shortcut to app()->registerDeferredProvider(). Use FQN.
     *
     * @var array
     */
    protected $deferredProviders = [];

    /**
     * Define the point where the $providers and $deferredProviders should be registered. accepts one of ON_REGISTER | ON_REGISTERED | ON_BOOT | ON_BOOTED.
     *
     * @var int
     */
    protected $registerProvidersOn = 'register'; // Inside service provider: 'register' or 'boot'. By application event: 'booting' or 'booted'

    /** @var string */
    protected $registerProvidersMethod = 'register'; // register | resolve

    /** @var int */
    protected $providersPluginPriority = 10;

    /**
     * startProvidersPlugin method.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startProvidersPlugin($app)
    {
        switch ($this->registerProvidersOn) {
            case 'register':
                $this->onRegister('providers', function () {
                    $this->handleProviders();
                });
                break;
            case 'booting':
                $this->app->booting(function () {
                    $this->handleProviders();
                });
                break;
            case 'boot':
                $this->onBoot('providers', function () {
                    $this->handleProviders();
                });
                break;
            case 'booted':
                $this->app->booted(function () {
                    $this->handleProviders();
                });
                break;
            default:
                throw new \LogicException('registerProvidersOn not valid');
                break;
        }
    }

    /**
     * handleProviders method.
     */
    protected function handleProviders()
    {
        // register deferred providers
        foreach ($this->deferredProviders as $provider) {
            $this->app->registerDeferredProvider($provider);
        }

        if ($this->registerProvidersMethod === 'register') {
            $this->registerProviders();
        } elseif ($this->registerProvidersMethod === 'resolve') {
            $this->resolveProviders();
        } else {
            throw new \LogicException('registerProvidersMethod not valid');
        }
    }

    /**
     * registerProviders method.
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * resolveProviders method.
     */
    protected function resolveProviders()
    {
        foreach ($this->providers as $provider) {
            $resolved = $this->resolveProvider($registered[] = $provider);
            $resolved->register();
            if ($this->registerProvidersOn === 'boot') {
                $this->app->call([$provider, 'boot']);
            }
        }
    }

    /**
     * resolveProvider method.
     *
     * @param $provider
     *
     * @return mixed
     */
    protected function resolveProvider($provider)
    {
        return $this->app->resolveProviderClass($registered[] = $provider);
    }
}
