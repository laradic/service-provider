<?php

namespace Laradic\ServiceProvider;

use Illuminate\Support\Arr;

class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    public function addListener($event, $listener)
    {
        $listeners   = Arr::get($this->listen, $event, []);
        $listeners[] = $listener;
        Arr::set($this->listen, $event, $listeners);
//        Arr::add($this->listen, $event, $listener);
        return $this;
    }

    public function addSubscriber($subscriber)
    {
        $this->subscribe[] = $subscriber;
        return $this;
    }

    protected bool $shouldDiscoverEvents = false;

    protected array $discoveryPaths = [];

    protected ?string $discoveryBasePath = null;

    public function setDiscoveryPaths(array $discoveryPaths)
    {
        $this->discoveryPaths = $discoveryPaths;
        return $this;
    }

    public function addDiscoveryPath(string $path)
    {
        $this->discoveryPaths[] = $path;
        return $this;
    }

    public function setDiscoveryBasePath(string $discoveryBasePath)
    {
        $this->discoveryBasePath = $discoveryBasePath;
        return $this;
    }

    public function setDiscoverEvents(bool $shouldDiscoverEvents)
    {
        $this->shouldDiscoverEvents = $shouldDiscoverEvents;
        return $this;
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return $this->shouldDiscoverEvents;
    }

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        return $this->discoveryPaths;
    }

    /**
     * Get the base path to be used during event discovery.
     *
     * @return string
     */
    protected function eventDiscoveryBasePath()
    {
        return $this->discoveryBasePath ?: base_path();
    }
}
