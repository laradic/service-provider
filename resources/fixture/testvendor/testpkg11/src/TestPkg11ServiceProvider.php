<?php
namespace TestVendor\TestPkg11;

use Laradic\ServiceProvider\ServiceProvider;

class TestPkg11ServiceProvider extends ServiceProvider
{
    protected $configFiles = [ 'testpkg11' ];

    protected $assetDirs = [ 'assets' => 'testpkg11' ];

    protected $translationDirs = [ 'lang' => 'testpkg11' ];

}