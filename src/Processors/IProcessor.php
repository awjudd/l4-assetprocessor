<?php

namespace Awjudd\AssetProcessor\Processors;

use Awjudd\AssetProcessor\Asset;

interface IProcessor
{
    /**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var        array
     */
    function getExtensions();

    /**
     * Retrieves the alias for the asset processor
     *
     * @return     string  Alias.
     */
    function getAlias();

    /**
     * Determines whether this processor handles the type of file
     *
     * @param      Asset  $asset  The asset we want to process
     * 
     * @return      boolean
     */
    function handles(Asset $asset);

    /**
     * Processes the asset
     *
     * @param      Asset  $asset  The asset to process
     * 
     * @return      Asset
     */
    function process(Asset $asset);

    /**
     * Get the output file name.
     *
     * @param      Asset  $asset  The asset we will be emitting.
     */
    function getOutputFileName(Asset $asset);
}