<?php
namespace Laradic\Tests\ServiceProvider;

use Laradic\Tests\ServiceProvider\Fixture\FixtureServiceProvider;

class ResolvePathsTest extends TestCase
{
    public function testPackagePath()
    {
        $fsp = new FixtureServiceProvider($this->app);
    }
}