<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2017. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2017 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
 */

namespace Laradic\ServiceProvider\Plugins;

/**
 * This is the class Resources.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
 * @mixin Paths
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait  Resources
{

    /** @var int */
    protected $resourcesPluginPriority = 15;


    /** @var string */
    protected $packagePath = '{rootDir}';

    /*
     |---------------------------------------------------------------------
     | Resources
     |---------------------------------------------------------------------
     |
     */

    /**
     * Path to resources directory
     *
     * @var string
     */
    protected $resourcesPath = '{packagePath}/resources';

    /**
     * Resource destination path, by default uses laravel's 'resources' directory
     *
     * @var string
     */
    protected $resourcesDestinationPath = '{path.resource}';


    /*
     |---------------------------------------------------------------------
     | Views
     |---------------------------------------------------------------------
     |
     */

    /**
     * View destination path, by default uses laravel's 'resources/views/vendor/{namespace}'
     *
     * @var string
     */
    protected $viewsDestinationPath = '{resourcesDestinationPath}/views/vendor/{namespace}';

    /**
     * Package views path
     *
     * @var string
     */
    protected $viewsPath = '{resourcesPath}/{dirName}';

    /**
     * A collection of directories in this package containing views.
     *
     * Using ['dirName' => 'namespace'] it binds the directory to a namespace.
     * This enables view('namespace::path.to.view') and includes it with vendor:publish
     *
     * @var array
     */
    protected $viewDirs = [ /* 'dirName' => 'namespace' */ ];


    /*
     |---------------------------------------------------------------------
     | Assets
     |---------------------------------------------------------------------
     |
     */

    /**
     * Assets destination path
     *
     * @var string
     */
    protected $assetsDestinationPath = '{path.public}/vendor/{namespace}';

    /**
     * Package assets path
     *
     * @var string
     */
    protected $assetsPath = '{resourcesPath}/{dirName}';

    /**
     * A collection of directories in this package containing assets.
     * ['dirName' => 'namespace']
     *
     * @var array
     */
    protected $assetDirs = [ /* 'dirName' => 'namespace' */ ];


    /*
     |---------------------------------------------------------------------
     | Configuration
     |---------------------------------------------------------------------
     |
     */

    /**
     * Collection of configuration files.
     *
     * @var array
     */
    protected $configFiles = [ ];

    /** @var string */
    protected $configDestinationPath = '{path.config}';

    /**
     * Path to the config directory
     *
     * @var string
     */
    protected $configPath = '{packagePath}/config';


    /*
     |---------------------------------------------------------------------
     | Translation
     |---------------------------------------------------------------------
     |
     */

    /** @var string */
    protected $translationDestinationPath = '{resourcesDestinationPath}/lang/vendor/{namespace}';

    /** @var string */
    protected $translationPath = '{resourcesPath}/{dirName}';

    /** @var array */
    protected $translationDirs = [ /* 'dirName' => 'namespace', */ ];


    public function _test()
    {

    }
    /*
     |---------------------------------------------------------------------
     | Database | Migrations | Seeds
     |---------------------------------------------------------------------
     |
     */

    /** @var string */
    protected $databaseDestinationPath = '{path.database}';

    /**
     * Path to database directory
     *
     * @var string
     */
    protected $databasePath = '{packagePath}/database';

    /**
     * Path to the migration destination directory
     *
     * @var string
     */
    protected $migrationDestinationPath = '{databaseDestinationPath}/migrations';

    /** @var string */
    protected $migrationsPath = '{databasePath}/{dirName}';

    /**
     * Array of directory names/paths relative to $databasePath containing migration files.
     *
     * @var array
     */
    protected $migrationDirs = [ /* 'dirName', */ ];

    /**
     * Migrations will be loaded automaticly. If you want to publish the migrations, this should be true
     * @var bool
     */
    protected $publishMigrations = false;


    /**
     * Path to the seeds destination directory
     *
     * @var string
     */
    protected $seedsDestinationPath = '{databaseDestinationPath}/seeds';

    /** @var string */
    protected $seedsPath = '{databasePath}/{dirName}';

    /**
     * Array of directory names/paths relative to $databasePath containing seed files.
     *
     * @var array
     */
    protected $seedDirs = [ /* 'dirName', */ ];



    /**
     * startPathsPlugin method
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startResourcesPlugin()
    {
        $this->requiresPlugins(Paths::class);
        $this->onBoot('resources', function () {
            foreach ( $this->viewDirs as $dirName => $namespace ) {
                $viewPath = $this->resolvePath('viewsPath', compact('dirName'));
                $this->loadViewsFrom($viewPath, $namespace);
                $this->publishes([ $viewPath => $this->resolvePath('viewsDestinationPath', compact('namespace')) ], 'views');
            }

            foreach ( $this->translationDirs as $dirName => $namespace ) {
                $transPath = $this->resolvePath('translationPath', compact('dirName'));
                $this->loadTranslationsFrom($transPath, $namespace);
                $this->publishes([ $transPath => $this->resolvePath('translationDestinationPath', compact('namespace')) ], 'translations');
            }

            foreach ( $this->assetDirs as $dirName => $namespace ) {
                $this->publishes([ $this->resolvePath('assetsPath', compact('dirName')) => $this->resolvePath('assetsDestinationPath', compact('namespace')) ], 'public');
            }

            foreach ( $this->migrationDirs as $dirName ) {
                $migrationPaths = $this->resolvePath('migrationsPath', compact('dirName'));
                $this->loadMigrationsFrom($migrationPaths);
                if($this->publishMigrations) {
                    $this->publishes([ $migrationPaths => $this->resolvePath('migrationDestinationPath') ], 'database');
                }
            }
            foreach ( $this->seedDirs as $dirName ) {
                $this->publishes([ $this->resolvePath('seedsPath', compact('dirName')) => $this->resolvePath('seedsDestinationPath') ], 'database');
            }
        });
    }
}