<?php
namespace Laradic\ServiceProvider\Plugins;

trait  Paths
{

    /*
     |---------------------------------------------------------------------
     | Resources properties
     |---------------------------------------------------------------------
     |
     */

    /**
     * Path to resources directory, relative to package root
     *
     * @var string
     */
    protected $resourcesPath = 'resources'; //'../resources';

    /**
     * Resource destination path, relative to base_path
     *
     * @var string
     */
    protected $resourcesDestinationPath = 'resources';


    /*
     |---------------------------------------------------------------------
     | Views properties
     |---------------------------------------------------------------------
     |
     */

    /**
     * View destination path, relative to base_path
     *
     * @var string
     */
    protected $viewsDestinationPath = '{resourcesDestinationPath}/views/vendor/{namespace}';

    /**
     * Package views path, relative to package root
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
     * Assets destination path, relative to base_path
     *
     * @var string
     */
    protected $assetsDestinationPath = 'public/vendor/{namespace}';

    /**
     * Package assets path, relative to package root folder
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

    /**
     * Path to the config directory, relative to package root folder
     *
     * @var string
     */
    protected $configPath = 'config';

    protected $configStrategy = 'defaultConfigStrategy';

    /*
     |---------------------------------------------------------------------
     | Database properties
     |---------------------------------------------------------------------
     |
     */

    /**
     * Path to the migration destination directory, relative to package root folder
     *
     * @var string
     */
    protected $migrationDestinationPath = '{databasePath}/migrations';

    /**
     * Path to the seeds destination directory, relative to package root folder
     *
     * @var string
     */
    protected $seedsDestinationPath = '{databasePath}/seeds';

    /**
     * Path to database directory, relative to  package root folder
     *
     * @var string
     */
    protected $databasePath = 'database';

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


    protected function startPathsPlugin($app)
    {

    }
}