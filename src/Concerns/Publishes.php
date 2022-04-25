<?php

namespace Laradic\ServiceProvider\Concerns;

/**
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 */
trait Publishes
{
    public $publish = [
        //[ [ __DIR__.'/../config/acme.php' => '{path.config}/acme.php'],['config'] ]
    ];

    private function initPublishesTrait()
    {
        $this->app->booting(function () {
            foreach ($this->publish as $publish) {
                $this->publishes($publish[ 0 ], count($publish) === 2 ? $publish[ 1 ] : null);
            }
        });
    }
}
