<?php
return [
    /**
     * Controller-specific 
     */
    'controller' => [
        /**
         * All of available asset types and their corresponding content types.
         * 
         * @var array
         */
        'content-types' => [
            'css' => 'text/css',
            'js' => 'text/javascript',
        ],

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
        'name' => '\Awjudd\Asset\Controllers\AssetController',
    ],

    /**
     * All of the cache settings
     */
    'cache' => [
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
         *              FALSE - combine all of the same-type of asset files together
         */
        'singular' => true,
    ],

    /**
     * Is the processing of these assets enabled?
     */
    'enabled' => [
        /**
         * What environments is this processing enabled in?  The environment name
         * here should match that from App::environment()
         * 
         * @var array
         */
        'environments' => [
            'production'
        ],
        /**
         * Should we force the processor to process the files?
         */
        'force' => false,
    ],

    'file' => [
        /**
         * Should the application error when there is a duplicate name.
         */
        'error-on-duplicate-name' => true,
    ],

    /**
     * An array containing all of the processors that the application will
     * be using.
     */
    'processors' => [
        'coffee'    => '\Awjudd\Asset\Processors\CoffeeScriptProcessor',
        'jsmin'     => '\Awjudd\Asset\Processors\JsMinifierProcessor',
        'sasscss'   => '\Awjudd\Asset\Processors\SassCSSProcessor',
        'lesscss'   => '\Awjudd\Asset\Processors\LessCSSProcessor',
        'cssmin'    => '\Awjudd\Asset\Processors\CSSMinifierProcessor',
    ],

];