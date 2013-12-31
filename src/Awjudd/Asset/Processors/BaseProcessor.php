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
     * Whether or not once we acquire the file, we will be processing it.
     * 
     * @var boolean
     */
    protected $processingEnabled = FALSE;

    /**
     * The instance of the object that will be handling the processing of the input files.
     * 
     * @var Object
     */
    protected static $instance = [];

    /**
     * The default constructor.
     * 
     * @param boolean $processingEnabled
     */
    public function __construct($processingEnabled)
    {
        // Set the processing enabled flag
        $this->processingEnabled = $processingEnabled;
    }

    /**
     * Returns an instance of the file processor.
     * 
     * @param boolean $processingEnabled
     * @return Awjudd\Asset\Interfaces\IAssetProcessor
     */
    public static function getInstance($processingEnabled)
    {
        // Figure out the class name
        $class = get_called_class();

        // Check if there already is an instance of the processor.
        if(!isset(static::$instance[$class]))
        {
            // There isn't, so instantiate it
            static::$instance[$class] = new $class($processingEnabled);
        }

        // Return the instance
        return static::$instance[$class];
    }

    /**
     * Used in order to retrieve a list of file extensions that this processor
     * handles.
     * 
     * @return array
     */
    public static function getAssociatedExtensions()
    {
        return static::$extensions;
    }

    /**
     * Used internally in order to write the current version of the file to disk.
     * 
     * @param string $contents The contents to write to the file.
     * @return string The fill path to the newly created file
     */
    protected function write($contents)
    {
        // Derive the MD5 of the contents
        $md5 = md5($contents);

        // Derive the filename that we will be writing to
        $directory = storage_path() . '/' . \Config::get('asset::cache.directory') . '/' . static::getAssetType() . '/';

        // Build the file name
        $filename = $directory . $md5;

        // Make sure that the folder exists
        if(!file_exists($directory))
        {
            // It doesn't, so make it (allow us to write)
            mkdir($directory, 0777, TRUE);
        }

        // Write the file to disk
        file_put_contents($filename, $contents);

        // Return the newly created file name
        return $filename;
    }
}