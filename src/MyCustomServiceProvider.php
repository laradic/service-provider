<?php
namespace Laradic\ServiceProvider;

class MyCustomServiceProvider extends CustomServiceProvider
{
    protected $configFiles = [
        'laradic.custom-provider',
    ];

    public function register()
    {
        $app = parent::register();

        $a = 'a';

        $this->getRootDir();

        return $app;
    }


}