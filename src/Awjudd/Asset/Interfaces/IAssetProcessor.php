<?php namespace Awjudd\Asset\Interfaces;

interface IAssetProcessor
{

    /**
     * Returns an instance of the file processor.
     * 
     * @return Awjudd\Asset\Interfaces\IAssetProcessor
     */
    public static function getInstance();

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
     * The full name of the asset to add to be processed.
     * 
     * @param $filename The full path to the file
     */
    public function addFile($filename);

    /**
     * Used to add an entire directory into the list of assets.
     * 
     * @param string $directory The directory
     * @param boolean $recursive Should we recursively scan through the directories?
     */
    public function addDirectory($directory, $recursive = FALSE);

    /**
     * Used to retrieve all of the assets processed through this object.
     * 
     * @return array Full paths to each of the assets
     */
    public function retrieve();


}