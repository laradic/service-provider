<?php
namespace Laradic\ServiceProvider\Plugins;

/**
 * This is the class Resources.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 * @mixin Paths
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait  Resources
{

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
    protected $resourcesDestinationPath = '{path.resources}';


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
     * ['dirName' => 'namespace']
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
    protected $translationPath = '{resourcePath}/{dirName}';

    /** @var array */
    protected $translationDirs = [ /* 'dirName' => 'namespace', */ ];


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
    protected $migrationsPath = '{databasePath}/migrations';

    /**
     * Array of directory names/paths relative to $databasePath containing migration files.
     *
     * @var array
     */
    protected $migrationDirs = [ /* 'dirName', */ ];


    /**
     * Path to the seeds destination directory
     *
     * @var string
     */
    protected $seedsDestinationPath = '{databaseDestinationPath}/seeds';

    /** @var string */
    protected $seedsPath = '{databasePath}/seeds';

    /**
     * Array of directory names/paths relative to $databasePath containing seed files.
     *
     * @var array
     */
    protected $seedDirs = [ /* 'dirName', */ ];


    /** @var int */
    protected $resourcesPluginPriority = 20;

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

            foreach ( $this->migrationDirs as $dirPath ) {
                $this->publishes([ $this->getDatabasePath($dirPath) => $this->resolvePath('migrationDestinationPath') ], 'database');
            }
            foreach ( $this->seedDirs as $dirPath ) {
                $this->publishes([ $this->getDatabasePath($dirPath) => $this->resolvePath('seedsDestinationPath') ], 'database');
            }
        });
    }
}