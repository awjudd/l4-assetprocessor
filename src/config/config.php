<?php
return array(

    /**
     * All attributse that ara available and their default options.
     * 
     * @var array
     */
    'attributes' => array(

        /**
         * All of the group-related information
         * 
         * @var array
         */
        'group' => array( 

            /**
             * The group name that all CDN related items will be grouped under
             * 
             * @var string
             */
            'cdn' => 'cdn',

            /**
             * The group name that is used by default.
             * 
             * @var string
             */
            'default' => 'default',

        ),
    ),

    /**
     * Any assets that should be auto-loaded.
     * 
     * @var array
     */
    'autoload' => array(

        /**
         * Any CDN-type assets to auto-load
         * 
         * @var array
         */
        'cdn' => array(
        ),

        /**
         * Any local assets that will need processing.
         * 
         * @var array
         */
        'local' => array(
        ),
    ),

    /**
     * Controller-specific configurations
     * 
     * @var array
     */
    'controller' => array(

        /**
         * All of available asset types and their corresponding content types.
         * 
         * @var array
         */
        'content-types' => array(
            'css' => 'text/css',
            'js' => 'text/javascript',
        ),

        /**
         * The name of the controller method used to handle the assets.
         * 
         * @var string
         */
        'method' => 'getIndex',

        /**
         * The name of the controller used to handle the assets.
         * 
         * @var string
         */
        'name' => '\Awjudd\AssetProcessor\Controllers\AssetProcessorController',
    ),

    /**
     * All of the cache settings
     * 
     * @var array
     */
    'cache' => array(

        /**
         * How long should the file remain cached for? (in seconds)
         * 
         * @var integer
         */
        'duration' => 86400,

        /**
         * The directory in the storage folder where the cached processed files
         * will be stored.
         * 
         * @var string
         */
        'directory' => storage_path('assets'),

        /**
         * The external path that is the final resting spot for the assets.
         * 
         * @var string
         */
        'external' => public_path('assets/generated'),

        /**
         * Should we be using a single file for each asset, or multiple?
         * 
         * @var boolean true - one file per provided source file
         *              false - combine all of the same-type of asset files together
         */
        'singular' => true,
    ),

    /**
     * Is the processing of these assets enabled?
     * 
     * @var array
     */
    'enabled' => array(

        /**
         * What environments is this processing enabled in?  The environment name
         * here should match that from App::environment()
         * 
         * @var array
         */
        'environments' => array(
            'production'
        ),

        /**
         * Should we force the processor to process the files?
         * 
         * @var boolean
         */
        'force' => false,
    ),

    /**
     * Contains any file related options
     * 
     * @var array
     */
    'file' => array(

        /**
         * Should the application error when there is a duplicate name.
         * 
         * @var boolean
         */
        'error-on-duplicate-name' => true,

        /**
         * Whether or not the application should error if an asset group doesn't exist
         * 
         * @var boolean
         */
        'error-on-missing-group' => false,
    ),

    /**
     * An array containing all of the processors that the application will
     * be using.
     * 
     * @var array
     */
    'processors' => array(

        /**
         * The name of the interface that all of the processors should implement.
         * 
         * @var string
         */
        'interface' => 'Awjudd\AssetProcessor\Interfaces\IAssetProcessor',

        /**
         * Contains all of the processors that the Asset Processor will use.
         * 
         * @var array
         */
        'types'     => array(
            'coffee'    => '\Awjudd\AssetProcessor\Processors\CoffeeScriptProcessor',
            'jsmin'     => '\Awjudd\AssetProcessor\Processors\JsMinifierProcessor',
            'sasscss'   => '\Awjudd\AssetProcessor\Processors\ScssCSSProcessor',
            'lesscss'   => '\Awjudd\AssetProcessor\Processors\LessCSSProcessor',
            'cssmin'    => '\Awjudd\AssetProcessor\Processors\CSSMinifierProcessor',
        ),
    ),

);