<?php
namespace Laradic\Tests\ServiceProvider\Fixture;

use Laradic\ServiceProvider\BaseServiceProvider;
use Laradic\ServiceProvider\Plugins\Bindings;
use Laradic\ServiceProvider\Plugins\Commands;
use Laradic\ServiceProvider\Plugins\Config;
use Laradic\ServiceProvider\Plugins\Events;
use Laradic\ServiceProvider\Plugins\Middleware;
use Laradic\ServiceProvider\Plugins\Paths;
use Laradic\ServiceProvider\Plugins\Resources;
use Laradic\ServiceProvider\Plugins\Providers;

abstract class ServiceProvider extends BaseServiceProvider
{
    use Bindings,
        Commands,
        Config,
        Events,
        Paths,
        Middleware,
        Resources,
        Providers;

}