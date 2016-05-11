<?php

namespace Awjudd\AssetProcessor\Processors;

use Awjudd\AssetProcessor\Asset\Asset;
use Awjudd\AssetProcessor\AssetProcessor;

class Processor
{
    /**
     * A complete list of the processors that are enabled.
     *
     * @var array
     */
    private static $_processors = [];

    /**
     * Process the asset.
     *
     * @param Asset $asset (description)
     */
    public static function process(Asset $asset)
    {
        // Get the list of processors
        $processors = static::getProcessorsForAsset($asset);

        $processedAsset = $asset;

        // Now that we have all of the processors, run them
        foreach($processors as $processor) {
            // Has the asset changed?
            if($processor->hasChanged($processedAsset)) {
                // Process the updated asset
                $newAsset = $processor->process($processedAsset);

                // Remove the old asset (if needed)
                if(static::isProcessedAsset($processedAsset)) {
                    unlink($processedAsset->getFullName());
                }
            }
            else {
                $processedAsset = $processor->createAssetFromFile($processor->getOutputFileName($processedAsset), $asset);
            }
        }

        return $processedAsset;
    }

    /**
     * Retrieves the folder which will be use for file output.
     * 
     * @return string The output directory
     */
    public static function getBaseOutputDirectory()
    {
        return AssetProcessor::storage_path('assets/');
    }

    /**
     * Get the public directory.
     *
     * @param      <type>  $path   (description)
     *
     * @return     string  Public directory.
     */
    public static function getPublicDirectory($path)
    {
        // Clean up the file name
        $directories = [
            config('asset-processor.paths.storage'),
            config('asset-processor.paths.asset-root'),
        ];

        $path = str_ireplace('//', '/', $path);

        // Remove any extra paths
        foreach($directories as $directory) {
            $path = str_ireplace($directory, '', $path);
        }

        return str_ireplace(public_path(), '', config('asset-processor.paths.public')) . $path;
    }

    /**
     * Determine if the asset is a processed asset
     *
     * @param      Asset    $asset  The asset in question
     *
     * @return     boolean  True if processed asset, False otherwise.
     */
    public static function isProcessedAsset(Asset $asset)
    {
        return stristr($asset->getFullName(), static::getBaseOutputDirectory());
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
            $processors = config('asset-processor.processors.types');

            // Loop through all of them
            foreach ($processors as $class) {
                // Get an instance
                static::$_processors[] = new $class();
            }
        }

        // Return the list
        return static::$_processors;
    }
}
