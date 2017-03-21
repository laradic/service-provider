<!--
title: Overview
subtitle: Laradic Service Provider
-->


The service provider can be extended and will provide a high level of abstraction.
All properties and methods have docblock documentation explaining how and what for its used.

### Basic Example
Lets say, our package file structure looks like this:
```text
- laradic
  - example-package
    - resources
      - views
        - layout.blade.php
        - page1.blade.php
      - assets
        - jquery.min.js
        - example-package.js
      - lang
        - en
          - general.php
    - database
      - migrations
      - seeds
    - config
      - example-package.php
    - src
      - ExamplePackageServiceProvider.php
    - tests
    - composer.json
    - phpunit.xml
```


<!--*codex:layout:row*-->
<!--*codex:layout:column('sm', '4')*-->
<!--*codex:phpdoc:list:property('Laradic\\ServiceProvider\\Plugins\\Resources', '', '$configFiles, $viewDirs, $assetDirs, $translationDirs, $migrationDirs, $seedDirs')*-->
<!--*codex:/layout:column*-->
<!--*codex:layout:column('sm', '8')*-->

```php
use Laradic\ServiceProvider\ServiceProvider;

class MyServiceProvider extends ServiceProvider {

    # uses $configPath to create the path and suffixes the my.package with .php
    protected $configFiles = [ 'example-package' ];
    
    // assigns the 'view' directory inside resources to namespace 'example-packages'.
    // that results in: view('example-packages::page1')
    // And using vendor:publish, this will be published to  
    protected $viewDirs = ['views' => 'example-package'];
    
    // assigns the 'assets' directory inside resources to vendor name 'example-package'.
    // that results in: asset('vendor/example-package/example-package.js')
    // And using vendor:publish, this will be published to 'public/vendor/example-package'
    protected $assetDirs = ['assets' => 'example-package'];

    // lang('example-package::general.title')
    protected $translationDirs = ['lang' => 'example-package'];
    
    // Array of directory names/paths relative to $databasePath containing migration files.
    protected $migrationDirs = ['migrations'];
    
    // Array of directory names/paths relative to $databasePath containing seed files.
    protected $seedDirs = ['seeds'];
}
```

<!--*codex:/layout:column*-->
<!--*codex:/layout:row*-->


```php
use Laradic\ServiceProvider\ServiceProvider;

class MyServiceProvider extends ServiceProvider {

    # uses the $dir and $configPath to create the path and suffixes the my.package with .php
    protected $configFiles = [ 'my.package' ];


    public function boot(){
        # When overriding the boot method, make sure to call the super method.
        # returns the Application instance
        $app = parent::boot();
    }

    public function register(){
        # When overriding the register method, make sure to call the super method.
        # returns the Application instance
        $app = parent::register();

    }
}
```