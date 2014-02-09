<?php
return array(

    /**
     * All attributse that ara available and their default options.
     * 
     * @var array
     */
    'attributes' => array(

        /**
         * The group name that is used by default.
         * 
         * @var string
         */
        'group' => 'default',
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
        'directory' => 'assets',

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
         */
        'error-on-duplicate-name' => true,
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