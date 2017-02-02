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

use Laradic\ServiceProvider\BaseServiceProvider;
use Laradic\Support\Str;
use Laradic\Support\Util;
use ReflectionClass;

/**
 * This is the class Commands.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait Commands
{

    /**
     * Collection of commands.
     *
     * @var array
     * @example
     * <?php
     * $new = new ServiceProvider;
     */
    protected $commands = [];

    /**
     * Commands that are found are bound in the container using this string as prefix
     * @var string
     */
    protected $commandPrefix = 'command.';

    /**
     * Collection of paths to search for commands
     * @var array
     */
    protected $findCommands = [];

    /**
     * If true, the $findCommands path will be searched recursively (all subdirectories will be scanned) for commands
     * @var bool
     */
    protected $findCommandsRecursive = false;

    /**
     *  Commands should extend
     * @var string
     */
    protected $findCommandsExtending = 'Symfony\Component\Console\Command\Command';

    protected $commandsPluginPriority = 50;

    /**
     * startCommandsPlugin method
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startCommandsPlugin($app)
    {

        $this->onRegister('commands', function ($app) {
            /** @var \Illuminate\Foundation\Application $app */
            // Commands
            if ( $app->runningInConsole() ) {
                foreach ( $this->findCommands as $path ) {
                    $dir     = path_get_directory((new ReflectionClass(get_called_class()))->getFileName());
                    $classes = $this->findCommandsIn(path_join($dir, $path), $this->findCommandsRecursive);

                    $this->commands = array_merge($this->commands, $classes);
                }
                if ( is_array($this->commands) && count($this->commands) > 0 ) {
                    $commands = [];
                    foreach ( $this->commands as $k => $v ) {
                        if ( is_string($k) ) {
                            $app[ $this->commandPrefix . $k ] = $app->share(function ($app) use ($k, $v) {
                                return $app->build($v);
                            });

                            $commands[] = $this->commandPrefix . $k;
                        } else {
                            $commands[] = $v;
                        }
                    }
                    $this->commands($commands);
                }
            }
        });
    }


    /**
     * findCommandsIn method
     *
     * @param      $path
     * @param bool $recursive
     *
     * @return array
     */
    protected function findCommandsIn($path, $recursive = false)
    {

        $classes = [];
        foreach ( $this->findCommandsFiles($path) as $filePath ) {

            //$class = $classFinder->findClass($filePath);

            $class = Util::getClassNameFromFile($filePath);
            if ( $class !== null ) {
                $namespace = Util::getNamespaceFromFile($filePath);
                if ( $namespace !== null ) {
                    $class = "$namespace\\$class";
                }
                $class   = Str::removeLeft($class, '\\');
                $parents = class_parents($class);

                if ( $this->findCommandsExtending !== null && in_array($this->findCommandsExtending, $parents, true) === false ) {
                    continue;
                }
                $ref = new \ReflectionClass($class);
                if ( $ref->isAbstract() ) {
                    continue;
                }
                $classes[] = Str::removeLeft($class, '\\');
            }
        }
        return $classes;
    }

    /**
     * findCommandsFiles method
     *
     * @param $directory
     *
     * @return array
     */
    protected function findCommandsFiles($directory)
    {
        $glob = glob($directory . '/*');

        if ( $glob === false ) {
            return [];
        }

        // To get the appropriate files, we'll simply glob the directory and filter
        // out any "files" that are not truly files so we do not end up with any
        // directories in our list, but only true files within the directory.
        return array_filter($glob, function ($file) {
            return filetype($file) === 'file';
        });
    }
}