<?php
namespace Laradic\ServiceProvider\Plugins;

trait Facades
{

    /**
     * @var array
     */
    protected $facades = [ /* 'Form' => Path\To\Facade::class */ ];

    protected $facadesPluginPriority = 60;

    protected function startFacadesPlugin($app)
    {
    }
}