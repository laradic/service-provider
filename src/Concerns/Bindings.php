<?php

namespace Laradic\ServiceProvider\Concerns;

/**
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 */
trait Bindings
{

    public $singletons = [
        // Class::class
        // 'name' => Class::class
    ];

    public $bindings = [
        // 'name' => Class::class
    ];

    public $aliases = [
        // 'name' => Class::class
    ];

    public $providers = [
        // MyServiceProvider::class
    ];

    public $commands = [
        //'command.my.name' => Command::class
        //Command::class
    ];

    private function initBindingsTrait()
    {
        $register = function ($method, $key, $value = null) {
            if (is_int($key)) {
                $this->app->{$method}($value);
            } elseif ($value === null) {
                $this->app->{$method}($key);
            } else {
                $this->app->{$method}($key, $value);
            }
        };
        // singleton and bindings are done in the application already
        foreach ($this->aliases as $key => $value) {
            $register('alias', $value, $key);
        }
        foreach ($this->providers as $provider) {
            $register('register', $provider);
        }
        foreach ($this->commands as $key => $value) {
            $register('bind', $key, $value);
        }
        $this->commands($this->commands);
    }

}
