<?php

namespace Awjudd\AssetProcessor\Processors;

use Awjudd\AssetProcessor\Asset\Asset;

interface IProcessor
{
    /**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var array
     */
    public function getExtensions();

    /**
     * Retrieves the alias for the asset processor.
     *
     * @return string Alias.
     */
    public function getAlias();

    /**
     * Determines whether this processor handles the type of file.
     *
     * @param Asset $asset The asset we want to process
     * 
     * @return bool
     */
    public function handles(Asset $asset);

    /**
     * Get the target file extension.
     *
     * @return     string  Target file extension.
     */
    public function getTargetExtension(Asset $asset);

    /**
     * Processes the asset.
     *
     * @param Asset $asset The asset to process
     * 
     * @return Asset
     */
    public function process(Asset $asset);

    /**
     * Get the output file name.
     *
     * @param Asset $asset The asset we will be emitting.
     */
    public function getOutputFileName(Asset $asset);
}
