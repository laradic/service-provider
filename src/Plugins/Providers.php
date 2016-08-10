<?php
namespace Laradic\ServiceProvider\Plugins;

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
    protected $registerProvidersOn = self::ON_REGISTER;

    protected $registerProvidersMethod = self::METHOD_REGISTER;


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


    protected function startProvidersPlugin($app)
    {

    }
}