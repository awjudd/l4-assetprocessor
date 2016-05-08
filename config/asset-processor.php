<?php

return [
    'checks'    => [
        /**
         * Whether or not you can have mixed asset types in groups.
         * 
         * @var     boolean
         */
        'allow-mixed-asset-groups'  => false,
    ],

    /**
     * An array which holds all of the asset processors that we will use in the application.
     * 
     * @var        array
     */
    'processors'    => [

        /**
         * The name of the interface that all of the processors should implement.
         * 
         * @var        string
         */
        'interface' => \Awjudd\AssetProcessor\Processors\IProcessor::class,

        /**
         * Contains all of the processors that the asset processor will use.
         * 
         * @var        array
         */
        'types' => [

        ],
    ],
];