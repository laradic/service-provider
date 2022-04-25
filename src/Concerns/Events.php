<?php

namespace Laradic\ServiceProvider\Concerns;


use Laradic\ServiceProvider\EventServiceProvider;

/**
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 */
trait Events
{
    public $listenerPaths = [
        // relative paths or absolute paths
        'Listener',
        'Listeners',
    ];

    public $listeners = [
        // EventClass::class => [
        //  ListenerClass::class
        // ]
    ];

    public $subscribers = [

    ];

    private function initEventsTrait()
    {
        $provider = new EventServiceProvider($this->app);
        foreach ($this->listeners as $event => $listeners) {
            foreach ($listeners as $listener) {
                $provider->addListener($event, $listener);
            }
        }
        foreach ($this->subscribers as $subscribers) {
            $provider->addSubscriber($subscribers);
        }
        foreach ($this->listenerPaths as $path) {
            $path = $this->callPrivateMethod('reflectionPath',$path);
            $provider->addDiscoveryPath($path);
        }
        $provider->setDiscoverEvents(true);
        $this->app->register($provider, true);
    }
}
