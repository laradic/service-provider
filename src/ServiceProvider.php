<?php
namespace Laradic\ServiceProvider;



abstract class ServiceProvider extends BaseServiceProvider
{
    use
        Plugins\Bindings,
        Plugins\Commands,
        Plugins\Config,
        Plugins\Events,
        Plugins\Facades,
        Plugins\Helpers,
        Plugins\Middleware,
        Plugins\Paths,
        Plugins\Providers,
        Plugins\Resources;
}