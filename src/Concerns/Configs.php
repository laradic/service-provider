<?php

namespace Laradic\ServiceProvider\Concerns;

/**
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 */
trait Configs
{
    public $configs = [
        // __DIR__.'/../config/acme.php' => 'acme'
    ];

    private function initConfigsTrait()
    {
        foreach ($this->configs as $path => $key) {
            $this->mergeConfigFrom($path, $key);
        }
    }

}
