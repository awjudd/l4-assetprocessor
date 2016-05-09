<?php

namespace Awjudd\AssetProcessor\Processors;

use Awjudd\AssetProcessor\Asset\Asset;

class Processor
{
    /**
     * A complete list of the processors that are enabled.
     *
     * @var array
     */
    private static $_processors = [];

    /**
     * Process.
     *
     * @param Asset $asset (description)
     */
    public static function process(Asset $asset)
    {
        // Get the list of processors
        $processors = static::getProcessorsForAsset($asset);

        $processed = $asset;

        // Now that we have all of the processors, run them
        foreach($processors as $processor) {
            // Process the updated asset
            $processed = $processor->process($processed);
        }
        
        return $processed;
    }

    /**
     * Retrieves the folder which will be use for file output.
     * 
     * @return string The output directory
     */
    public static function getBaseOutputDirectory()
    {
        return '../../storage/assets/';
    }

    /**
     * Retrieves the list of processors for the specified asset
     *
     * @param      Asset  $asset  The asset
     *
     * @return     array  Processors for asset.
     */
    private static function getProcessorsForAsset(Asset $asset)
    {
        // Grab the list of processors
        $processors = static::getProcessors();

        $assetProcessors = [];

        // Cycle through all possible processors
        foreach ($processors as $processor) {
            // Does the processor handle the asset type?
            if ($processor->handles($asset)) {
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
     * @return array Processors.
     */
    private static function getProcessors()
    {
        // Are there any processors loaded?
        if (empty(static::$_processors)) {
            // There aren't, so let's build them
            $processors = [
                \Awjudd\AssetProcessor\Processors\Common\FinalOutputProcessor::class,
            ];

            $mappings = [];

            // Loop through all of them
            foreach ($processors as $class) {
                // Get an instance
                static::$_processors[] = $class::getInstance();
            }
        }

        // Return the list
        return static::$_processors;
    }
}
