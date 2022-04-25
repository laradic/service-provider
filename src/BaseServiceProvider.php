<?php

namespace Laradic\ServiceProvider;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClass;

class BaseServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private string $currentDir;

    private Dispatcher $dispatcher;

    private ReflectionClass $reflection;

    private bool $started = false;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->dispatcher = $app->make(Dispatcher::class);
        $this->reflection = new ReflectionClass($this);
        $this->currentDir = dirname($this->reflection->getFileName());
        $this->startIfNotStarted();
    }

    private function startIfNotStarted()
    {
        if (true === $this->started) {
            return;
        }
        $this->started = true;
        $this->callTraits();
    }

    private function callTraits()
    {
        foreach ($this->getTraits() as $trait) {
            if (method_exists(get_called_class(), $methodName = 'init' . class_basename($trait) . 'Trait')) {
                $this->callPrivateMethod($methodName);
            }
        }
    }

    private function getTraits()
    {
        return array_values(class_uses_recursive(get_called_class()));
    }

    private function reflectionPath($path)
    {
        if (Path::isAbsolute($path)) {
            return $path;
        }
        return Path::join($this->currentDir, $path);
    }

    protected function callPrivateMethod(string $methodName, mixed ...$arguments)
    {
        $method = $this->reflection->getMethod($methodName);
        $method->setAccessible(true);
        $result = $method->invoke($this, ...$arguments);
        $method->setAccessible(false);
        return $result;
    }

    /** @var array */
    private $resolvedPaths;

    protected function resolvePath($name, array $extras = [])
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

    private function getPaths()
    {
        $paths = Arr::dot(['path' => $this->getLaravelPaths()]);
        collect(array_keys(get_class_vars(get_class($this))))->filter(function ($propertyName) {
            return Str::endsWith($propertyName, 'Path');
        })->each(function ($propertyName) use (&$paths) {
            $paths[ $propertyName ] = $this->{$propertyName};
        });
//        $paths[ 'packagePath' ] = $this->getRootDir();

        return $paths;
    }

    private function getLaravelPaths()
    {
        $paths = [
            'app' => $this->app[ 'path' ],
            'envFile' => $this->app->environmentFilePath(),
            'env' => $this->app->environmentPath(),
            'cached' => [
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
