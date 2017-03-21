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

trait Facades
{
    /**
     * @var array
     */
    protected $facades = [/* 'Form' => Path\To\Facade::class */];

    protected $facadesPluginPriority = 60;

    protected function startFacadesPlugin($app)
    {
    }
}
