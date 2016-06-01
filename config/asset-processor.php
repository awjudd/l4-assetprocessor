<?php

return [
    'checks' => [

    ],

    /*
     * An array which holds all of the asset processors that we will use in the application.
     * 
     * @var        array
     */
    'processors' => [

        /*
         * The name of the interface that all of the processors should implement.
         * 
         * @var        string
         */
        'interface' => \Awjudd\AssetProcessor\Processors\IProcessor::class,

        /*
         * Contains all of the processors that the asset processor will use.
         * 
         * @var        array
         */
        'types' => [
            \Awjudd\AssetProcessor\Processors\StyleSheet\LessCssProcessor::class,
            \Awjudd\AssetProcessor\Processors\StyleSheet\ScssProcessor::class,
            \Awjudd\AssetProcessor\Processors\StyleSheet\CssMinifierProcessor::class,

            \Awjudd\AssetProcessor\Processors\JavaScript\CoffeeScriptProcessor::class,
            \Awjudd\AssetProcessor\Processors\JavaScript\JavaScriptMinifierProcessor::class,
            \Awjudd\AssetProcessor\Processors\JavaScript\JavaScriptPackerProcessor::class,

            \Awjudd\AssetProcessor\Processors\Common\FinalProcessor::class,
        ],
    ],

    'paths' => [
        /*
         * The folder where the assets will be stored / emitted to.
         */
        'storage' => storage_path('assets'),

        /*
         * The root path for any assets that the site has.
         */
        'asset-root' => resource_path('assets'),

        /*
         * The path in the web-root where the symbolic link will be placed.
         */
        'public' => public_path('/assets/generated'),
    ],
];
