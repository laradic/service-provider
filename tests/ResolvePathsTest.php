<?php
namespace Laradic\Tests\ServiceProvider;

use TestVendor\TestPkg11\TestPkg11ServiceProvider;

class ResolvePathsTest extends TestCase
{
    public function testPackagePath()
    {
        $p = new TestPkg11ServiceProvider($this->app);
        $p->register();

        $dirName               = 'assets';
        $namespace             = 'testpkg11';
        $rootDir               = $p->getRootDir();
        $assetsPath            = $p->resolvePath('assetsPath', compact('dirName', 'namespace'));
        $assetsDestinationPath = $p->resolvePath('assetsDestinationPath', compact('dirName', 'namespace'));
        $a                     = 'a';
    }
}