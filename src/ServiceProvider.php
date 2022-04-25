<?php

namespace Laradic\ServiceProvider;

use Laradic\ServiceProvider\Concerns\Bindings;
use Laradic\ServiceProvider\Concerns\Configs;
use Laradic\ServiceProvider\Concerns\Events;
use Laradic\ServiceProvider\Concerns\Facades;
use Laradic\ServiceProvider\Concerns\Policies;
use Laradic\ServiceProvider\Concerns\Publishes;
use Laradic\ServiceProvider\Concerns\Routes;
use Laradic\ServiceProvider\Concerns\Runs;
use Laradic\ServiceProvider\Concerns\Seeding;

class ServiceProvider extends BaseServiceProvider
{
    use Events;
    use Bindings;
    use Configs;
    use Events;
    use Facades;
    use Policies;
    use Publishes;
    use Routes;
    use Runs;
    use Seeding;
}
