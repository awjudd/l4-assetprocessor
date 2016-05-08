<?php

namespace Awjudd\AssetProcessor\Processors;

use Awjudd\AssetProcessor\Asset;

abstract class BaseProcessor implements IProcessor
{
    /**
     * The instance of the asset processor.
     */
    private static $instance;

    /**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var        array
     */
    abstract function getExtensions();

    /**
     * Retrieves the alias for the asset processor
     *
     * @return     string  Alias.
     */
    abstract function getAlias();

    /**
     * Processes the asset
     *
     * @param      Asset  $asset  The asset to process
     * 
     * @return      Asset
     */
    abstract function process(Asset $asset);

    /**
     * Retrieves an instance of the 
     *
     * @return     BaseProcessor
     */
    public static function getInstance()
    {
        // Grab the class name
        $class = get_called_class();

        // Do we already have an instance?
        if(!isset(static::$instance)) {
            // We don't, so make one
            static::$instance = new $class($alias);
        }

        return static::$instance;
    }

    /**
     * Get the output file name.
     *
     * @param      Asset  $asset  The asset we will be emitting.
     */
    public function getOutputFileName(Asset $asset)
    {

    }

    /**
     * Retrieves the folder which will be use for file output.
     * 
     * @return     string The output directory
     */
    public function getBaseOutputDirectory()
    {
        return '../../storage/assets/';
    }
}