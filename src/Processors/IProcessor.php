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
     * Determines if the processor handles style sheet
     *
     * @return     boolean  True if style sheet processor, False otherwise.
     */
    function isStylesheetProcessor();

    /**
     * Determines if the processor handles JavaScript
     *
     * @return     boolean  True if JavaScript processor, False otherwise.
     */
    function isJavaScriptProcessor();

    /**
     * Determines whether this processor handles the type of file
     *
     * @param      Asset  $asset  (description)
     */
    function handles(Asset $asset);
}