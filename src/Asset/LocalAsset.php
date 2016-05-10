<?php

namespace Awjudd\AssetProcessor\Asset;

use SplFileInfo;
use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Awjudd\AssetProcessor\Processors\Processor;

class LocalAsset extends Asset
{
    /**
     * The name of the file that will be processed.
     * 
     * @var SplFileInfo
     */
    private $_file;

    /**
     * The name / url to the asset file.
     * 
     * @var string
     */
    private $_filename;

    /**
     * The processed version of the asset
     *
     * @var        LocalAsset
     */
    private $_processed = null;

    private $_baseAsset = null;

    /**
     * Instantiates the asset object.
     *
     * @param string $filename The name of the file we will be processing.
     */
    private function __construct($filename, Asset $base = null)
    {
        $this->_filename = $filename;

        $this->_baseAsset = $base;

        $this->_file = new SplFileInfo($filename);

        // Fill in the file metadata
        $this->deriveMetadata();
    }

    /**
     * Creates an array of assets.
     * 
     * @param string $path
     */
    public static function create($path, Asset $base = null)
    {
        $file = new SplFileInfo($path);

        // Is the file a single file?
        if($file->isFile()) {
            // It is, so just return it
            return [
                new LocalAsset($path, $base)
            ];
        }

        if(!$file->isDir()) {
            // The user didn't provide a single file, so we can't handle it
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid file provided (%s)', $path
                )
            );
        }

        // Grab the list of files
        $files = static::getFiles($file);

        $assets = [];

        // Loop through the files
        foreach($files as $file) {
            // Create the asset
            $assets[] = new LocalAsset($file->getPathname());
        }

        return $assets;
    }

    /**
     * Retrieves the name of the asset file.
     *
     * @return     string  Name.
     */
    public function getName()
    {
        if(!is_null($this->_baseAsset)) {
            return $this->_baseAsset->getName();
        }

        return $this->_file->getFileName();
    }

    /**
     * Retrieves the file extension for the asset.
     * 
     * @return     string
     */
    public function getExtension()
    {
        return $this->_file->getExtension();
    }

    /**
     * Retrieves the file's full name
     * 
     * @return  string
     */
    public function getFullName()
    {
        return $this->_file->getPathname();
    }

    /**
     * Processes the asset.
     * 
     * @return Asset The updated asset object
     */
    public function process()
    {
        if(is_null($this->_processed)) {
            $this->_processed = Processor::process($this);

            if(is_array($this->_processed)) {
                $this->_processed = $this->_processed[0];
            }
        }

        // Return the helper
        return $this->_processed;
    }

    /**
     * Retrieves the public path for the asset.
     *
     * @return string Public path.
     */
    public function getPublicPath()
    {
        return Processor::getPublicDirectory($this->process()->getFullName());
    }

    /**
     * Retrieves the last modified time of an asset.
     *
     * @return     int  Modified time.
     */
    public function getModifiedTime()
    {
        // Is there a base asset?
        if(!is_null($this->_baseAsset)) {
            // There is, so return that asset's modified time
            return $this->_baseAsset->getModifiedTime();
        }

        return $this->_file->getMTime();
    }

    /**
     * Derives the metadata that is required for the asset.
     */
    protected function deriveMetadata()
    {
        $this->_isJavaScript |= in_array($this->_file->getExtension(), ['js', 'coffee']);
        $this->_isStyleSheet |= in_array($this->_file->getExtension(), ['css', 'less', 'scss']);
    }

    /**
     * Retrieves all of the files that are being handled by the asset.
     *
     * @param SplFileInfo $file The current file
     *
     * @return array Files.
     */
    private static function getFiles(SplFileInfo $file)
    {
        // Is the file a directory?
        if (!$file->isDir()) {
            // It isn't a directory, so just return it
            return [
                $file->getExtension() => [$file],
            ];
        }

        $files = [];

        // Grab the directory
        $dir = new RecursiveDirectoryIterator($file->getPathName());

        // Recursively loop through it
        $iterator = new RecursiveIteratorIterator($dir);

        foreach ($iterator as $filename => $info) {
            // Make sure it's not pointing at itself
            if (in_array($info->getFilename(), ['.', '..'])) {
                continue;
            }

            // Add the files to the end of the array
            $files[] = $info;
        }

        // Return the files
        return $files;
    }
}
