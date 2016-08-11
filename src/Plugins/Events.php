<?php
namespace Laradic\ServiceProvider\Plugins;

/**
 * This is the class Events.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait Events
{
    /**
     * startEventsPlugin method
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startEventsPlugin($app)
    {
    }


    /**
     * on method
     *
     * @param $events
     * @param $handler
     */
    protected function on($events, $handler)
    {
        $dispatcher = $this->app->make('events');
        $dispatcher->listen($events, $handler);
    }
}