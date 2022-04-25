<?php

namespace Laradic\ServiceProvider\Concerns;

/**
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 */
trait Runs
{
    public $call = [
        // Class with CallableTrait
        // Class::class => 'register'
        // Class::class => 'booting'
        // Class::class => 'boot'
        // Class::class => 'booted'
    ];

    private function initRunsTrait()
    {
        $call = function ($key, $value = null) {
            if (is_int($key)) {
                return $value::dispatch();
            }
            if ($value === 'register') {
                return $key::dispatch();
            }
            if ($value === 'booting') {
                $this->app->booting(fn() => $key::dispatch());
            } elseif ($value === 'boot') {
                $this->booting(fn() => $key::dispatch());
            } elseif ($value === 'booted') {
                $this->app->booted(fn() => $key::dispatch());
            }
        };
        foreach ($this->call as $key => $value) {
            $call($key, $value);
        }
    }

}
