<?php
return [

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
         * @var boolean TRUE - one file per provided source file
         *              FALSE - combine all of the same-type of asset files together
         */
        'singular' => FALSE,
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
        'error-on-duplicate-name' => TRUE
    ],

    /**
     * An array containing all of the processors that the application will
     * be using.
     */
    'processors' => [
        'jsmin' => '\Awjudd\Asset\Processors\JsMinifierProcessor',
    ],

];