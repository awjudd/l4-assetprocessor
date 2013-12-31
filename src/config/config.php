<?php
return [
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
    /**
     * An array containing all of the processors that the application will
     * be using.
     */
    'processors' => [
        'jsmin' => '\Awjudd\Asset\Processors\JsMinifierProcessor',
    ],

];