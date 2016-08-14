<?php
namespace Laradic\ServiceProvider\Plugins;

/**
 * This is the class Bindings.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait Bindings
{

    /**
     * Enables strict checking of provided bindings, aliases and singletons. Checks if the given items are correct. Set to false if
     *
     * @var bool
     */
    protected $strict = true;

    /**
     * Names with associated class that will be bound into the container
     *
     * @var array
     */
    protected $bindings = [ ];

    /**
     * Collection of classes to register as singleton
     *
     * @var array
     */
    protected $singletons = [ ];

    /**
     * Collection of classes to register as share. Does not make an alias if the value is a class, as is the case with $shared.
     *
     * @var array
     */
    protected $share = [ ];

    /**
     * Collection of classes to register as share. Also registers an alias if the value is a class, as opposite to $share.
     *
     * @var array
     */
    protected $shared = [ ];

    /**
     * Wealkings are bindings that perform a bound check and will not override other bindings
     *
     * @var array
     */
    protected $weaklings = [ ];

    /**
     * Collection of aliases.
     *
     * @var array
     */
    protected $aliases = [ ];

    protected $bindingsPluginPriority = 30;


    /**
     * startBindingsPlugin method
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startBindingsPlugin($app)
    {
        /** @var \Illuminate\Foundation\Application $app */
        $this->requiresPlugins(Commands::class, Events::class);

        $this->onRegister('bindings', function ($app) {

            // Container bindings and aliases
            foreach ( $this->bindings as $binding => $class ) {
                $this->app->bind($binding, $class);
            }

            foreach ( $this->weaklings as $binding => $class ) {
                $this->bindIf($binding, $class);
            }

            foreach ( [ 'share' => $this->share, 'shared' => $this->shared ] as $type => $bindings ) {
                foreach ( $bindings as $binding => $class ) {
                    $this->share($binding, $class, [ ], $type === 'shared');
                }
            }

            foreach ( $this->singletons as $binding => $class ) {
                if ( $this->strict && !class_exists($class) && !interface_exists($class) ) {
                    throw new \Exception(get_called_class() . ": Could not find alias class [{$class}]. This exception is only thrown when \$strict checking is enabled");
                }
                $this->app->singleton($binding, $class);
            }

            foreach ( $this->aliases as $alias => $full ) {
                if ( $this->strict && !class_exists($full) && !interface_exists($full) ) {
                    throw new \Exception(get_called_class() . ": Could not find alias class [{$full}]. This exception is only thrown when \$strict checking is enabled");
                }
                $this->app->alias($alias, $full);
            }
        });
    }


    /**
     * Registers a binding if it hasn't already been registered.
     *
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @param  bool                 $shared
     * @param  bool|string|null     $alias
     *
     * @return void
     */
    protected function bindIf($abstract, $concrete = null, $shared = true, $alias = null)
    {
        if ( !$this->app->bound($abstract) ) {
            $concrete = $concrete ?: $abstract;

            $this->app->bind($abstract, $concrete, $shared);
        }
    }

    /**
     * Register a class so it's shared. Optionally create an alias for it.
     *
     * @param       $binding
     * @param       $class
     * @param array $params
     * @param bool  $alias
     */
    protected function share($binding, $class, $params = [ ], $alias = false)
    {
        if ( is_string($class) ) {
            $closure = function ($app) use ($class, $params) {
                return $app->build($class, $params);
            };
        } else {
            $closure = $class;
        }
        $this->app[ $binding ] = $this->app->share($closure);
        if ( $alias ) {
            $this->app->alias($binding, $class);
        }
    }
}