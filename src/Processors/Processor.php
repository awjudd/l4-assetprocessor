<?php

namespace Awjudd\AssetProcessor\Processor;

use Awjudd\AssetProcessor\Asset;
use Awjudd\AssetProcessor\Processors\IProcessor;

class Processor
{
    /**
     * A complete list of the processors that are enabled.
     *
     * @var        array
     */
    private static $_processors = [];

    /**
     * Process
     *
     * @param      Asset  $asset  (description)
     */
    public static function process(Asset $asset)
    {
        // Get the list of processors
        $processors = static::$getProcessorsForAsset($asset);
    }

    private static function getProcessorsForAsset(Asset $asset)
    {
        // Grab the list of processors
        $processors = static::getProcessors();

        $assetProcessors = [];

        // Cycle through all possible processors
        foreach($processors as $processor) {
            // Does the processor handle the asset type?
            if($processor->handles($asset)) {
                // It does, so add it in
                $assetProcessors[] = $processor;
            }
        }
        
        // Return the specific processors
        return $assetProcessors;
    }

    /**
     * Retrieves a complete list of the processors that are enabled.
     *
     * @return     array  Processors.
     */
    private static function getProcessors()
    {
        // Are there any processors loaded?
        if(empty(static::$_processors)) {
            // There aren't, so let's build them
            $processors = config('asset-processor.processors.types');

            $mappings = [];

            // Loop through all of them
            foreach($processors as $class) {
                // Get an instance
                static::$_processors[] = $class::getInstance();
            }

            // Now that we have everything, store it
            static::$_processors = $mappings;
        }

        // Return the list
        return static::$_processors;
    }
}