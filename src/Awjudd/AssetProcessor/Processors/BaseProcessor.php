<?php namespace Awjudd\AssetProcessor\Processors;

use Awjudd\AssetProcessor\Interfaces\IAssetProcessor;

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
    protected $processingEnabled = false;

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
     * Determines whether or not we need to reprocess the file.
     * 
     * @param string $filename
     * @return boolean
     */
    public function shouldProcess($filename)
    {
        // Derive the name of the file to use
        $destination = $this->getFinalName($filename);

        // Does the destination file exist?
        if(!\File::exists($destination))
        {
            // It doesn't exist, so we will need to process
            return true;
        }

        // Otherwise compare the date stamps
        $destinationChanged = \File::lastModified($destination);
        $currentChanged = \File::lastModified($filename);

        // Which one was last modified later
        if($currentChanged > $destinationChanged)
        {
            // Our destination is older than the actual file, so update
            return true;
        }

        // Touch the file so that we know it's up-to-date
        touch($destination);

        // We don't need to process the file (the old version will do)
        return false;
    }

    /**
     * Used in order to get the final full path for the file that this process
     * will generate.
     * 
     * @param $filename The filename to process
     * @return string The full name for the file that we are processing
     */
    protected function getFinalName($filename)
    {
        return $this->getOutputDirectory(basename($filename)) . $this->getOutputFileName($filename);
    }

        /**
     * Used internally in order to write the current version of the file to disk.
     * 
     * @param string $contents The contents to write to the file.
     * @return string The fill path to the newly created file
     */
    protected function write($contents, $assetFileName)
    {
        // Derive the filename that we will be writing to
        $directory = $this->getOutputDirectory($assetFileName);

        // Build the file name
        $filename = self::getOutputFileName($assetFileName);

        // The full file path
        $fullpath = $directory.$filename;

        // Make sure that the folder exists
        if(!file_exists($directory))
        {
            // It doesn't, so make it (allow us to write)
            mkdir($directory, 0777, true);
        }

        // Write the file to disk
        file_put_contents($fullpath, $contents);

        // Return the newly created file name
        return $fullpath;
    }

    /**
     * Used to get the derived output directory.
     * 
     * @param string $assetFileName The name of the file that we are processing
     * @return string The full path to the directory to write to
     */
    protected function getOutputDirectory($assetFileName)
    {
        return storage_path() . '/' . \Config::get('asset::cache.directory') . '/' 
            . static::getAssetType() . '/' . basename($assetFileName) . '/';
    }

    /**
     * Used to derive the file name that we will be saving this file as.
     * 
     * @return string The name that this step of the processing will take
     */
    protected function getOutputFileName($assetFileName)
    {
        return md5(static::getType() . $assetFileName);
    }
}