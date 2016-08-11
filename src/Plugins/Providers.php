<?php
namespace Laradic\ServiceProvider\Plugins;

use Laradic\ServiceProvider\ProviderActionPoint;
use Laradic\ServiceProvider\ProviderRegisterMethod;

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
    protected $registerProvidersOn = ProviderActionPoint::REGISTER;

    protected $registerProvidersMethod = ProviderRegisterMethod::REGISTER;


    /**
     * @var array
     */
    protected $facades = [ /* 'Form' => Path\To\Facade::class */ ];

    /**
     * Collection of helper php files. To be required either on register or boot. [$filePath => self::ON_REGISTERED].
     * Accepts values: ON_REGISTER | ON_REGISTERED | ON_BOOT | ON_BOOTED
     *
     * @var array
     */
    protected $helpers = [ /* $filePath => 'boot/register'  */ ];

    /**
     * Declaring the method named here will make it so it will be called on application booting
     *
     * @var string
     */
    protected $bootingMethod = 'booting';

    /**
     * Declaring the method named here will make it so it will be called when the application has booted
     *
     * @var string
     */
    protected $bootedMethod = 'booted';


    /**
     * startProvidersPlugin method
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startProvidersPlugin($app)
    {
        $this->onRegister('providers', $this->registerProvidersOn, function ($app) {
            $this->tryRequireHelpers($this->registerProvidersOn);
            $this->tryRegisterProviders($this->registerProvidersOn);
        });
    }


    /**
     * This will check method
     *
     * @param $on
     */
    protected function tryRequireHelpers($on)
    {
        foreach ( $this->helpers as $filePath => $for ) {
            if ( $on === $for ) {
                require_once path_join($this->getRootDir(), $filePath);
            }
        }
    }

    /**
     * tryRegisterProviders method
     *
     * @param $on
     */
    protected function tryRegisterProviders($on)
    {
        if ( $on === $this->registerProvidersOn && $this->registerProvidersMethod === ProviderRegisterMethod::REGISTER ) {
            // FIRST register all given providers
            foreach ( $this->providers as $provider ) {
                $this->app->register($provider);
            }

            foreach ( $this->deferredProviders as $provider ) {
                $this->app->registerDeferredProvider($provider);
            }
        } elseif ( $this->registerProvidersMethod === ProviderRegisterMethod::RESOLVE ) {
            foreach ( $this->providers as $provider ) {
                $resolved = $this->app->resolveProviderClass($registered[] = $provider);
                if ( $on === ProviderActionPoint::REGISTER ) {
                    $resolved->register();
                } elseif ( $on === ProviderActionPoint::BOOT ) {
                    $this->app->call([ $provider, 'boot' ]);
                }
            }
        }
    }

}