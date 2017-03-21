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

trait Helpers
{
    protected $helpersPluginPriority = 70;

    protected function startHelpersPlugin()
    {
    }

    /**
     * Collection of helper php files. To be required either on register or boot. [$filePath => self::ON_REGISTERED].
     * Accepts values: ON_REGISTER | ON_REGISTERED | ON_BOOT | ON_BOOTED.
     *
     * @var array
     */
    protected $helpers = [/* $filePath => 'boot/register'  */];

    /**
     * This will check method.
     *
     * @param $on
     */
    protected function tryRequireHelpers($on)
    {
        foreach ($this->helpers as $filePath => $for) {
            if ($on === $for) {
                require_once path_join($this->getRootDir(), $filePath);
            }
        }
    }
}
