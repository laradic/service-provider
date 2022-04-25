<?php

namespace Laradic\ServiceProvider\Concerns;

use Illuminate\Support\Arr;

/**
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 */
trait Seeding
{
    private static $seeds = [];

    public $seeders = [
        //Class::class => ['parameters']
        //Class::class
    ];

    private function initSeedingTrait()
    {
        foreach ($this->seeders as $seeder => $parameters) {
            if (is_int($seeder)) {
                static::$seeds[ $parameters ] = [];
            } else {
                static::$seeds[ $seeder ] = Arr::wrap($parameters);
            }
        }
    }

    public static function getSeeders()
    {
        return static::$seeds;
    }
}
