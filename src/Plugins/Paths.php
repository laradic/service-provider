<?php
namespace Laradic\ServiceProvider\Plugins;

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
    protected $pathsPluginPriority = 0;

    protected $resolvedPaths;


    /**
     * resolvePath method
     *
     * @todo
     *
     * @param       $pathPropertyName
     * @param array $extras
     *
     * @return string
     */
    protected function resolvePath($name, array $extras = [ ])
    {
        if ( $this->resolvedPaths === null ) {
            $this->resolvedPaths = $this->getPaths();
        }
        if ( str_contains($this->resolvedPaths[ $name ], [ '{', '}' ]) ) {
            preg_match_all('/{(.*?)}/', $this->resolvedPaths[ $name ], $matches);
            foreach ( $matches[ 0 ] as $i => $match ) {
                $var = $matches[ 1 ][ $i ];
                if ( false === array_key_exists($var, $this->resolvedPaths) ) {
                    continue;
                }
                $this->resolvedPaths[ $name ] = str_replace($match, $this->resolvePath($var, $extras), $this->resolvedPaths[ $name ]);
            }

            foreach ( $extras as $key => $val ) {
                $this->resolvedPaths[ $name ] = str_replace('{' . $key . '}', $val, $this->resolvedPaths[ $name ]);
            }
        }
        return $this->resolvedPaths[ $name ];
    }

    private function getPaths()
    {
        $paths = array_dot([ 'path' => $this->getLaravelPaths() ]);
        collect(array_keys(get_class_vars(get_class($this))))->filter(function ($propertyName) {
            return ends_with($propertyName, 'Path');
        })->each(function ($propertyName) use (&$paths) {
            $paths[ $propertyName ] = $this->{$propertyName}; //
        });
        $paths[ 'packagePath' ] = $this->getRootDir();
        return $paths;
    }

    private function getLaravelPaths()
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
        $paths[ 'resource' ] = resource_path();

        return $paths;
    }
}