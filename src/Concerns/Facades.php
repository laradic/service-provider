<?php

namespace Laradic\ServiceProvider\Concerns;

use Illuminate\Foundation\AliasLoader;

/**
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 */
trait Facades
{
    public $facades = [
        // 'Name' => Class::name,
    ];
    private function initFacadesTrait()
    {
        if ( ! empty($this->facades)) {
            $loader  = AliasLoader::getInstance();
            $aliases = $loader->getAliases();
            $loader->setAliases(array_merge($aliases, $this->facades));
            $loader->setRegistered(false);
            $loader->register();
        }
    }

}
