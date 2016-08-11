<?php
namespace Laradic\ServiceProvider\Plugins;

/**
 * This is the class Paths.
 *
 * @property-read \Illuminate\Foundation\Application $app
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 *
 * @package        Laradic\ServiceProvider
 * @author         CLI
 * @copyright      Copyright (c) 2015, CLI. All rights reserved
 */
trait  Resources
{

    protected $packagePath = '{rootDir}';

    #protected $destinationPath = '{path.base}';

    /*
     |---------------------------------------------------------------------
     | Resources properties
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
     | Views properties
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
     | Assets properties
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
     | Configuration properties
     |---------------------------------------------------------------------
     |
     */

    /**
     * Collection of configuration files.
     *
     * @var array
     */
    protected $configFiles = [ ];

    protected $configDestinationPath = '{path.config}';

    /**
     * Path to the config directory
     *
     * @var string
     */
    protected $configPath = '{packagePath}/config';

    /*
     |---------------------------------------------------------------------
     | Database properties
     |---------------------------------------------------------------------
     |
     */

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

    protected $migrationsPath = '{databasePath}/migrations';

    /**
     * Path to the seeds destination directory
     *
     * @var string
     */
    protected $seedsDestinationPath = '{databaseDestinationPath}/seeds';

    protected $seedsPath = '{databasePath}/seeds';

    /**
     * Array of directory names/paths relative to $databasePath containing seed files.
     *
     * @var array
     */
    protected $seedDirs = [ /* 'dirName', */ ];

    /**
     * Array of directory names/paths relative to $databasePath containing migration files.
     *
     * @var array
     */
    protected $migrationDirs = [ /* 'dirName', */ ];

    /**
     * startPathsPlugin method
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function startResourcesPlugin($app)
    {
        $this->requiresPlugins(Paths::class);
    }


}