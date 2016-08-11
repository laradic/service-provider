<?php
namespace Laradic\ServiceProvider;

abstract class CustomServiceProvider extends BaseServiceProvider
{
    use
        Plugins\Resources,
        Plugins\Config,
        Plugins\Bindings,
        Plugins\Commands,
        Plugins\Paths,
        Plugins\Events,
        Plugins\Middleware,
        Plugins\Providers;


}