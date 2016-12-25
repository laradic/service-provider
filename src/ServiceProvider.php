<?php
namespace Laradic\ServiceProvider;

/**
 * This is the class ServiceProvider.
 *
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 *
 * @property \Illuminate\Contracts\Config\Repository $config
 */
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