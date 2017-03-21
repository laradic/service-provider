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

namespace Laradic\ServiceProvider\Exception;

use RuntimeException;

class ProviderPluginDependencyException extends RuntimeException
{
    public static function plugin($providerPlugin, $requiresPlugin)
    {
        return new static("The Service Provider plugin [{$providerPlugin}] requires [{$requiresPlugin}]. This usually means your custom abstract service provider should add [use ${requiresPlugin}].");
    }
}
