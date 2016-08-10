<?php
namespace Laradic\ServiceProvider;

abstract class CustomServiceProvider extends BaseServiceProvider
{
    use
        Plugins\Bindings,
        Plugins\Commands,
        Plugins\Events,
        Plugins\Middleware,
        Plugins\Paths,
        Plugins\Providers;


}