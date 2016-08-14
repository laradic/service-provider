<?php
namespace Laradic\ServiceProvider\Plugins;

use Laradic\Support\Util;

/**
 * This is the class Paths.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait Paths
{
    protected $resolvedPaths;

    /**
     * resolvePath method
     *
     * @todo
     *
     * @param string $pathPropertyName
     * @param array  $extras
     *
     * @return string
     */
    protected function resolvePath($pathPropertyName, array $extras = [ ])
    {
        $resolvedPaths = $this->getResolvedPaths();

        $extras[ 'path' ] = [
            'app'     => $this->app[ 'path' ],
            'envFile' => $this->app->environmentFilePath(),
            'env'     => $this->app->environmentPath(),
            'cached'  => [
                'compile'  => $this->app->getCachedCompilePath(),
                'config'   => $this->app->getCachedConfigPath(),
                'routes'   => $this->app->getCachedRoutesPath(),
                'services' => $this->app->getCachedServicesPath(),
            ],
        ];
        foreach ( [ 'base', 'lang', 'config', 'public', 'storage', 'database', 'bootstrap' ] as $key ) {
            $extras[ 'path' ][ $key ] = $this->app[ 'path.' . $key ];
        }

        return Util::template($resolvedPaths[ $pathPropertyName ], $extras);
    }

    protected function getLaravelPaths()
    {
        $paths = [
            'app'     => $this->app[ 'path' ],
            'envFile' => $this->app->environmentFilePath(),
            'env'     => $this->app->environmentPath(),
            'cached'  => [
                'compile'  => $this->app->getCachedCompilePath(),
                'config'   => $this->app->getCachedConfigPath(),
                'routes'   => $this->app->getCachedRoutesPath(),
                'services' => $this->app->getCachedServicesPath(),
            ],
        ];
        foreach ( [ 'base', 'lang', 'config', 'public', 'storage', 'database', 'bootstrap' ] as $key ) {
            $paths[ $key ] = $this->app[ 'path.' . $key ];
        }
        return $paths;
    }

    /**
     * resolvePaths method
     *
     * @todo
     * @return array
     */
    protected function getResolvedPaths()
    {
        if ( null === $this->resolvedPaths ) {
            //$this->resolveDirectories();

            $paths = array_dot([ 'path' => $this->getLaravelPaths() ]);
            // Collect all path properties and put them into $paths associatively using propertyName => propertyValue
            collect(array_keys(get_class_vars(get_class($this))))->filter(function ($propertyName) {
                return ends_with($propertyName, 'Path');
            })->each(function ($propertyName) use (&$paths) {
                $paths[ $propertyName ] = $this->{$propertyName}; //
            });

            $paths[ 'packagePath' ] = $this->getRootDir();

            // Use the paths to generate parsed paths, resolving all the {vars}
            $this->resolvedPaths = collect($paths)->transform(function ($path) use ($paths) {
                return Util::template($path, $paths);
            })->toArray();
        }
        return $this->resolvedPaths;
    }
}