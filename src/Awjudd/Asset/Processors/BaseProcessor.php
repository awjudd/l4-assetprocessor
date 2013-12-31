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
    protected static $instance = NULL;

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
        // Check if there already is an instance of the processor.
        if(self::$instance === NULL)
        {
            // Figure out the class name
            $class = get_called_class();

            // There isn't, so instantiate it
            self::$instance = new $class($processingEnabled);
        }

        // Return the instance
        return self::$instance;
    }

    /**
     * Used to add an entire directory into the list of assets.
     * 
     * @param string $filename The filename to process
     * @param boolean $recursive Should we recursively scan through the directories?
     */
    public function add($filename, $recursive = FALSE)
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
        return static::$extensions;
    }
}