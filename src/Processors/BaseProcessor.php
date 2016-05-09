<?php

namespace Awjudd\AssetProcessor\Processors;

use Awjudd\AssetProcessor\Asset\Asset;

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
     * Retrieves an instance of the.
     *
     * @return BaseProcessor
     */
    public static function getInstance()
    {
        // Grab the class name
        $class = get_called_class();

        // Do we already have an instance?
        if (!isset(static::$instance)) {
            // We don't, so make one
            static::$instance = new $class($alias);
        }

        return static::$instance;
    }

    /**
     * Get the output file name.
     *
     * @param Asset $asset The asset we will be emitting.
     */
    public function getOutputFileName(Asset $asset)
    {
        return sprintf(
            '%s/%s-%s-%s.%s',
            Processor::getBaseOutputDirectory(),
            str_replace('.' . $asset->getExtension(), '', $asset->getName()),
            $this->getAlias(),
            time(),
            $asset->getExtension()
        );
    }
}
