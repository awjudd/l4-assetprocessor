<?php namespace Awjudd\Asset\Processors;

use Awjudd\Asset\Interfaces\IAssetProcessor;

abstract class BaseProcessor implements IAssetProcessor
{
    /**
     * An array containing all of the file extensions that this processor needs
     * to use.
     * 
     * @var array
     */
    public static $extensions = [];

    /**
     * The instance of the object that will be handling the processing of the input files.
     * 
     * @var Object
     */
    protected static $instance = NULL;

    /**
     * Returns an instance of the file processor.
     * 
     * @return Awjudd\Asset\Interfaces\IAssetProcessor
     */
    public static function getInstance()
    {
        // Check if there already is an instance of the processor.
        if(self::$instance === NULL)
        {
            // Figure out the class name
            $class = get_called_class();

            // There isn't, so instantiate it
            self::$instance = new $class();
        }

        // Return the instance
        return self::$instance;
    }

    /**
     * Used to add an entire directory into the list of assets.
     * 
     * @param string $directory The directory
     * @param boolean $recursive Should we recursively scan through the directories?
     */
    public function addFile($filename)
    {

    }

    /**
     * The full name of the asset to add to be processed.
     * 
     * @param $filename The full path to the file
     */
    public function addDirectory($directory, $recursive = FALSE)
    {

    }

    /**
     * Used in order to retrieve a list of file extensions that this processor
     * handles.
     * 
     * @return array
     */
    public static function getAssociatedExtensions()
    {
        return self::$extensions;
    }
}