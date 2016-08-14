Laradic Service Provider
========================

[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

A general support package for the Laravel 5 framework. Laradic Support provides flexible and reusable components of code for commonly used functionality as well as the means to customize the default Laravel 5 folder structure.

The package follows the FIG standards PSR-1, PSR-2, and PSR-4 to ensure a high level of interoperability between shared PHP code.

Quick Installation
------------------
Begin by installing the package through Composer.

```bash
composer require laradic/service-provider=~1.0
```

Documentation
-------------

The code you write in your Service Providers is often very repetative and feels like you've already done so a hundreth times.

**Laradic ServiceProvider** might just be the thing you where looking for. 
It's a modular, configurable and very extendable way of creating providers. 
I think some examples might better explain it.
  
  
### Examples

#### Resources Example

A comparison of handling resources.

**The "default" way**
Use predefined methods inside your `register` and/or `boot` directory to hook up to the `vendor:publish` command.  
```php
use Illuminate\Support\ServiceProvider;
class MyServiceProvider extends ServiceProvider {
    public function boot(){
        // config: blade-extensions
        $publishPath = function_exists('config_path') ? config_path('blade_extensions.php') : base_path('config/blade_extensions.php');
        $this->publishes([ __DIR__ . '/../config/blade_extensions.php' => $publishPath ], 'config');
    
        // views: blade-ext
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'blade-ext');
        $this->publishes([ __DIR__ . '/../resources/views' => resource_path('views/vendor/blade-ext') ], 'views');
        
        // migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        // translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/trans', 'blade-ext');
        $this->publishes([__DIR__.'/../resources/trans' => resource_path('lang/vendor/blade-ext') ]);
        
        // assets
        $this->publishes([ __DIR__.'/../resources/assets' => public_path('vendor/blade-ext')], 'public');
    }

    public function register(){
        $this->mergeConfigFrom(__DIR__ . '/../config/blade_extensions.php', 'blade_extensions');
    }
}
```

**The "Laradic" way**
Override properties to define key values.  
```php
use Laradic\ServiceProvider\ServiceProvider;
class MyServiceProvider extends ServiceProvider{
    protected $configFiles = ['blade_extensions'];
    protected $viewDirs = ['views' => 'blade-ext'];
    protected $migrationDirs = ['migrations'];
    protected $translationDirs = ['trans' => 'blade-ext'];
    protected $assetDirs = ['assets' => 'blade-ext'];
} 
```

I can already hear you say "Wait, what! That's probably gonna force me to use a opinionated directory structure".

Calm down, you've just seen a very, very small part of Laradic Service Providers. 
The next example shows   


## Prioritties
10 config
20 helpers
30 providers
40 middlewares
50 bindings
60 commands
70 facades