<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2017. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2017 (c) Robin Radic
 * @license   https://laradic.mit-license.org The MIT License
 */

namespace Laradic\ServiceProvider\Plugins;

use Illuminate\Contracts\Events\Dispatcher;


/**
 * This is the class Events.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
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
    protected $listen = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    protected $events = [
//        \Acme\Events\JobDeleted::class => [
//            \Acme\Listeners\MarkJobAsDeleted::class
//        ]
    ];

    /**
     * startEventsPlugin method.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startEventsPlugin($app)
    {
        $this->onRegister('events', function ($app) {
            $events = $this->app->make(Dispatcher::class);

            foreach ($this->events() as $event => $listeners) {
                foreach ($listeners as $listener) {
                    $events->listen($event, $listener);
                }
            }
//        })
//        $this->onBoot('events', function ($app) {
//            $events = $this->app->make('events');

            foreach ($this->listens() as $event => $listeners) {
                foreach ($listeners as $listener) {
                    $events->listen($event, $listener);
                }
            }

            foreach ($this->subscribe as $subscriber) {
                $events->subscribe($subscriber);
            }
        });
    }

    /**
     * on method.
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

    /**
     * events method
     *
     * @return array {
     *  string[]
     * }
     */
    public function events()
    {
        return $this->events;
    }
}
