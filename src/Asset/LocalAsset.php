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
     * Instantiates the asset object.
     *
     * @param string $filename The name of the file we will be processing.
     */
    private function __construct($filename)
    {
        $this->_filename = $filename;

        $this->_file = new SplFileInfo($filename);

        // Fill in the file metadata
        $this->deriveMetadata();
    }

    /**
     * Creates an array of assets.
     * 
     * @param string $path
     */
    public static function create($path)
    {
        $file = new SplFileInfo($path);

        // Is the file a single file?
        if($file->isFile()) {
            // It is, so just return it
            return [
                new LocalAsset($path)
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
        return $this->_file->getFileName();
    }

    /**
     * Processes the asset.
     * 
     * @return Asset The updated asset object
     */
    public function process()
    {
        // Return the helper
        return Processor::process($this);
    }

    /**
     * Retrieves the public path for the asset.
     *
     * @return string Public path.
     */
    public function getPublicPath()
    {
        // Return the file name
        return '';
    }

    /**
     * Grabs the files by a specific extension.
     * 
     * @var string The extension to look for
     * 
     * @return array
     */
    public function byExtension($extension)
    {
        $files = $this->getFiles();

        return isset($files[$extension]) ? $files[$extension] : [];
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
