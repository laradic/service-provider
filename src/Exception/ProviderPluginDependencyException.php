<?php
namespace Laradic\ServiceProvider\Exception;

use RuntimeException;

class ProviderPluginDependencyException extends RuntimeException
{
    public static function plugin($providerPlugin, $requiresPlugin)
    {
        return new static("The Service Provider plugin [{$providerPlugin}] requires [{$requiresPlugin}]. This usually means your custom abstract service provider should add [use ${requiresPlugin}].");
    }
}