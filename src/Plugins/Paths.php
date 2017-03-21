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

namespace Laradic\ServiceProvider\Plugins;

/**
 * This is the class Paths.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
/**
 * This is the class Paths.
 *
 * @package Laradic\ServiceProvider\Plugins
 * @author  Robin Radic
 */
trait Paths
{
    /** @var int */
    protected $pathsPluginPriority = 0;

    /** @var */
    protected $resolvedPaths;

    /**
     * resolvePath method.
     *
     * @todo
     *
     * @param       $pathPropertyName
     * @param array $extras
     *
     * @return string
     */
    public function resolvePath($name, array $extras = [])
    {
        if ($this->resolvedPaths === null) {
            $this->resolvedPaths = $this->getPaths();
        }
        if (str_contains($this->resolvedPaths[ $name ], ['{', '}'])) {
            preg_match_all('/{(.*?)}/', $this->resolvedPaths[ $name ], $matches);
            foreach ($matches[ 0 ] as $i => $match) {
                $var = $matches[ 1 ][ $i ];
                if (false === array_key_exists($var, $this->resolvedPaths)) {
                    continue;
                }
                $this->resolvedPaths[ $name ] = str_replace($match, $this->resolvePath($var, $extras), $this->resolvedPaths[ $name ]);
            }

            foreach ($extras as $key => $val) {
                $this->resolvedPaths[ $name ] = str_replace('{'.$key.'}', $val, $this->resolvedPaths[ $name ]);
            }
        }

        return $this->resolvedPaths[ $name ];
    }

    /**
     * getPaths method
     *
     * @return array
     */
    private function getPaths()
    {
        $paths = array_dot(['path' => $this->getLaravelPaths()]);
        collect(array_keys(get_class_vars(get_class($this))))->filter(function ($propertyName) {
            return ends_with($propertyName, 'Path');
        })->each(function ($propertyName) use (&$paths) {
            $paths[ $propertyName ] = $this->{$propertyName};
        });
        $paths[ 'packagePath' ] = $this->getRootDir();

        return $paths;
    }

    /**
     * getLaravelPaths method
     *
     * @return array
     */
    private function getLaravelPaths()
    {
        $is54 = version_compare(\Illuminate\Foundation\Application::VERSION, '5.4.0', '>');
        $paths = [
            'app' => $this->app[ 'path' ],
            'envFile' => $this->app->environmentFilePath(),
            'env' => $this->app->environmentPath(),
            'cached' => [
                'compile' => $is54 ? null : $this->app->getCachedCompilePath(),
                'config' => $this->app->getCachedConfigPath(),
                'routes' => $this->app->getCachedRoutesPath(),
                'services' => $this->app->getCachedServicesPath(),
            ],
        ];
        foreach (['base', 'lang', 'config', 'public', 'storage', 'database', 'bootstrap'] as $key) {
            $paths[ $key ] = $this->app[ 'path.'.$key ];
        }
        $paths[ 'resource' ] = resource_path();

        return $paths;
    }
}
