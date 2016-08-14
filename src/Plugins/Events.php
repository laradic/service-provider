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
    protected $eventsPluginPriority = 10;

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [ ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [ ];

    /**
     * startEventsPlugin method
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startEventsPlugin($app)
    {
        $this->onBoot('events', function ($app) {
            $events = $this->app->make('events');

            foreach ( $this->listens() as $event => $listeners ) {
                foreach ( $listeners as $listener ) {
                    $events->listen($event, $listener);
                }
            }

            foreach ( $this->subscribe as $subscriber ) {
                $events->subscribe($subscriber);
            }
        });
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


    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}