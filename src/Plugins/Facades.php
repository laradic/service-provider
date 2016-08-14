<?php
namespace Laradic\ServiceProvider\Plugins;

trait Facades
{

    /**
     * @var array
     */
    protected $facades = [ /* 'Form' => Path\To\Facade::class */ ];

    protected $facadesPluginPriority = 50;

    protected function startFacadesPlugin($app)
    {
    }
}