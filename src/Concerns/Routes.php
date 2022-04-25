<?php

namespace Laradic\ServiceProvider\Concerns;

use Illuminate\Support\Arr;

/**
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 */
trait Routes
{
    /**
     * @ var array = static::routesExample()
     * @var array<string, array{method:string, namespace:string,name:string,controller:string,uses:string,middleware:array,model:string,where:array,as:string,prefix:string}>
     */
    public $routes = [    ];

    private function initRoutesTrait()
    {
        $router = $this->app[ 'router' ];
        foreach ($this->routes as $uri => $data) {
            if (str_contains($uri, ':')) {
                [ $method, $uri ] = explode(':', $uri);
            } else {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $data   = array_replace([
                    'method' => 'any',
                ], $data);
                $method = Arr::pull($data, 'method');
            }
            $router->{$method}($uri, $data);
        }
    }
}
