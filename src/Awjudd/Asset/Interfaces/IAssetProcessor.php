<?php namespace Awjudd\Asset\Interfaces;

interface IAssetProcessor
{

    /**
     * The default constructor.
     * 
     * @param boolean $processingEnabled
     */
    public function __construct($processingEnabled);

    /**
     * Returns an instance of the file processor.
     * 
     * @param boolean $processingEnabled
     * @return Awjudd\Asset\Interfaces\IAssetProcessor
     */
    public static function getInstance($processingEnabled);

    /**
     * The type of processor this instance is.
     * 
     * @return string
     */
    public static function getType();

    /**
     * The description of this processor.
     * 
     * @var string
     */
    public static function getDescription();

    /**
     * Used in order to retrieve a list of file extensions that this processor
     * handles.
     * 
     * @return array
     */
    public static function getAssociatedExtensions();

    /**
     * Determines the classification of an asset.
     * 
     * @return string
     */
    public static function getAssetType();

    /**
     * Used in order to process the input file.  After processing this input
     * file, it will return a new file name for the rest of the process to use
     * if needed.
     * 
     * @param string $filename
     * @return string
     */
    public function process($filename);

    /**
     * Determines whether or not we need to reprocess the file.
     * 
     * @param string $filename
     * @return boolean
     */
    public function shouldProcess($filename);

    /**
     * Whether or not we should bypass the process filter
     * 
     * @return boolean
     */
    public function bypassProcess();
}