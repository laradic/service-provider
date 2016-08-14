<?php
namespace Laradic\ServiceProvider\Plugins;


/**
 * This is the class Providers.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait  Providers
{
    /**
     * These Service Providers will be registered. Basicaly providing a shortcut to app()->register(). Use FQN.
     *
     * @var array
     */
    protected $providers = [ ];

    /**
     * These Service Providers will be registered as deferred. Basicaly providing a shortcut to app()->registerDeferredProvider(). Use FQN.
     *
     * @var array
     */
    protected $deferredProviders = [ ];

    /**
     * Define the point where the $providers and $deferredProviders should be registered. accepts one of ON_REGISTER | ON_REGISTERED | ON_BOOT | ON_BOOTED
     *
     * @var int
     */
    protected $registerProvidersOn = 'register'; // register | boot

    /** @var string */
    protected $registerProvidersMethod = 'register'; // register | resolve

    /** @var int */
    protected $providersPluginPriority = 10;


    /**
     * startProvidersPlugin method
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startProvidersPlugin($app)
    {
        if ( $this->registerProvidersOn === 'register' ) {
            $this->onRegister('providers', function () {
                $this->handleProviders();
            });
        } elseif ( $this->registerProvidersOn === 'boot' ) {
            $this->onBoot('providers', function () {
                $this->handleProviders();
            });
        } else {
            throw new \LogicException('registerProvidersOn not valid');
        }
    }

    /**
     * handleProviders method
     */
    protected function handleProviders()
    {
        // register deferred providers
        foreach ( $this->deferredProviders as $provider ) {
            $this->app->registerDeferredProvider($provider);
        }

        if ( $this->registerProvidersMethod === 'register' ) {
            $this->registerProviders();
        } elseif ( $this->registerProvidersMethod === 'resolve' ) {
            $this->resolveProviders();
        } else {
            throw new \LogicException('registerProvidersMethod not valid');
        }
    }

    /**
     * registerProviders method
     */
    protected function registerProviders()
    {
        foreach ( $this->providers as $provider ) {
            $this->app->register($provider);
        }
    }

    /**
     * resolveProviders method
     */
    protected function resolveProviders()
    {
        foreach ( $this->providers as $provider ) {
            $resolved = $this->resolveProvider($registered[] = $provider);
            $resolved->register();
            if ( $this->registerProvidersOn === 'boot' ) {
                $this->app->call([ $provider, 'boot' ]);
            }
        }
    }

    /**
     * resolveProvider method
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