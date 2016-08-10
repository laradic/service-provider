<?php
namespace Laradic\ServiceProvider\Plugins;

trait Bindings
{

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


    protected function startBindingsPlugin()
    {


    }
}