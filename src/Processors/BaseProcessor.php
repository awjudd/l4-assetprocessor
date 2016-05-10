<?php

namespace Awjudd\AssetProcessor\Processors;

use Awjudd\AssetProcessor\Asset\Asset;
use Awjudd\AssetProcessor\AssetProcessor;
use Awjudd\AssetProcessor\Asset\LocalAsset;

abstract class BaseProcessor implements IProcessor
{
    /**
     * The instance of the asset processor.
     */
    protected static $instance;

    /**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var array
     */
    abstract public function getExtensions();

    /**
     * Retrieves the alias for the asset processor.
     *
     * @return string Alias.
     */
    abstract public function getAlias();

    /**
     * Processes the asset.
     *
     * @param Asset $asset The asset to process
     * 
     * @return Asset
     */
    abstract public function process(Asset $asset);

    /**
     * Get the target file extension.
     *
     * @return     string  Target file extension.
     */
    abstract public function getTargetExtension(Asset $asset);

    /**
     * Determines whether this processor handles the type of file.
     *
     * @param Asset $asset The asset we want to process
     * 
     * @return bool
     */
    public function handles(Asset $asset)
    {
        return in_array($asset->getExtension(), $this->getExtensions());
    }

    /**
     * Reads the asset from the file system.
     *
     * @param      Asset   $asset  The asset to read
     *
     * @return     string  Contents of the asset file
     */
    public function read(Asset $asset)
    {
        // Read the file
        return file_get_contents($asset->getFullName());
    }

    /**
     * Writes the asset out to the file system.
     *
     * @param      Asset   $asset     (description)
     * @param      <type>  $contents  (description)
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function write(Asset $asset, $contents)
    {
        // Derive the output name
        $outputFile = $this->getOutputFileName($asset);

        // Make the parent directory if needed
        if(!file_exists(dirname($outputFile))) {
            mkdir(dirname($outputFile), 0777, true);
        }

        // Write the contents out
        file_put_contents($outputFile, $contents);

        // Create the output asset
        return $this->createAssetFromFile($outputFile, $asset);
    }

    /**
     * Returns an instance of the asset from a file.
     *
     * @param      <type>  $file   (description)
     *
     * @return     Asset
     */
    public function createAssetFromFile($file, Asset $base)
    {
        return LocalAsset::create($file, $base)[0];
    }

    /**
     * Determine if it has changed.
     *
     * @param      Asset    $asset  (description)
     *
     * @return     boolean  True if has changed, False otherwise.
     */
    public function hasChanged(Asset $asset)
    {
        return !file_exists($this->getOutputFileName($asset));
    }

    /**
     * Get the output file name.
     *
     * @param Asset $asset The asset we will be emitting.
     */
    public function getOutputFileName(Asset $asset)
    {
        $outputDirectory = Processor::getBaseOutputDirectory();
        $currentDirectory = str_ireplace(AssetProcessor::resource_path('/assets/'), '', dirname($asset->getFullName()));

        if(stristr($currentDirectory, $outputDirectory)) {
            $outputDirectory = $currentDirectory;
        }
        else {
            $outputDirectory .= $currentDirectory;
        }

        return sprintf(
            '%s/%s-%s-%s.%s',
            $outputDirectory,
            str_replace('.' . $asset->getExtension(), '', $asset->getName()),
            $this->getAlias(),
            $asset->getModifiedTime(),
            $this->getTargetExtension($asset)
        );
    }
}
