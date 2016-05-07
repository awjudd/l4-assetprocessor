<?php

namespace Awjudd\AssetProcessor\Processors;

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
     * Determines if the processor handles style sheet
     *
     * @return     boolean  True if style sheet processor, False otherwise.
     */
    public function isStylesheetProcessor()
    {
        return false;
    }

    /**
     * Determines if the processor handles JavaScript
     *
     * @return     boolean  True if JavaScript processor, False otherwise.
     */
    public function isJavaScriptProcessor()
    {
        return false;
    }
}