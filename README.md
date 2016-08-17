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
The full documentation can be found [here](https://la.radic.nl/service-provider) 

Quick overview
--------------

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
class MyServiceProvider extends \Illuminate\Support\ServiceProvider {
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
class MyServiceProvider extends \Laradic\ServiceProvider\ServiceProvider {
    protected $configFiles = ['blade_extensions'];
    protected $viewDirs = ['views' => 'blade-ext'];
    protected $migrationDirs = ['migrations'];
    protected $translationDirs = ['trans' => 'blade-ext'];
    protected $assetDirs = ['assets' => 'blade-ext'];
} 
```

Of course, it's entirely possible to re-configure paths as well. You don't have to stick to the default directory structure. 
But that's beyond the scope of this example and can be found in the documentation.

#### Bindings and Commands Example

**The "default" way**

**The "Laradic" way**
Override properties to define key values.  
```php
class MyServiceProvider extends \Laradic\ServiceProvider\ServiceProvider {
    // Will look for all commands inside the 'Console' directory relative to this service provider
    protected $findCommands = ['Console']; 
} 
```
or
```php
class MyServiceProvider extends \Laradic\ServiceProvider\ServiceProvider {
    // Register by FQN
    protected $commands = [Console\MyCommand::class]; 
    // or by custom binding name  
    protected $commands = ['commands.my.command' => Console\MyCommand::class];  
} 
```

Again, more variations and options exist which are beyond the scope of this example. Go read the [documentation](https://la.radic.nl/service-provider)

### Register and Boot
The `ServiceProvider` does not override existing functionality. Its entirely possible to let it ignore all the xtra stuff.
If you want to use the `register` and `boot` methods while 