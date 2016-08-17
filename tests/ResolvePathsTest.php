<?php
namespace Laradic\Tests\ServiceProvider;

use Laradic\Tests\ServiceProvider\Fixture\FixtureServiceProvider;
use TestVendor\TestPkg11\TestPkg11ServiceProvider;

class ResolvePathsTest extends TestCase
{
    public function testPackagePath()
    {
        $p = new TestPkg11ServiceProvider($this->app);
        $p->register();

        $rootDir = $p->getRootDir();
        $paths   = $p->resolvePath('assetsPath');
        $paths2  = $p->resolvePath('assetsDestinationPath');
        $a       = 'a';
    }
}