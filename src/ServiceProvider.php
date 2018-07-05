<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2017. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2017 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
 */

namespace Laradic\ServiceProvider;

/**
 * This is the class ServiceProvider.
 *
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 *
 * @property \Illuminate\Contracts\Config\Repository $config
 */
abstract class ServiceProvider extends BaseServiceProvider
{
    use Plugins\Bindings,
        Plugins\Commands,
        Plugins\Config,
        Plugins\Events,
        Plugins\Facades,
        Plugins\Helpers,
        Plugins\Routing,
        Plugins\Paths,
        Plugins\Providers,
        Plugins\Resources;

}
