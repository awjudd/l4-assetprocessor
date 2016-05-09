<?php

namespace Awjudd\AssetProcessor\Asset;

use SplFileInfo;
use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

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
    public function __construct($filename)
    {
        $this->_filename = $filename;

        $this->_file = new SplFileInfo($filename);

        // Is the file valid?
        if (!$this->_file->isFile() && !$this->_file->isDir()) {
            // The user didn't provide a single file, so we can't handle it
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid file provided (%s)', $filename
                )
            );
        }

        // Fill in the file metadata
        $this->deriveMetadata();
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
     * Derives the metadata that is required for the asset.
     */
    protected function deriveMetadata()
    {
        // Get the list of files
        $files = $this->getFiles();

        // Loop through them deriving the metadata
        foreach ($files as $file) {
            $this->deriveFileMetadata($file);
        }
    }

    /**
     * Retrieves all of the files that are being handled by the asset.
     *
     * @param SplFileInfo $file The current file
     *
     * @return array Files.
     */
    private function getFiles(SplFileInfo $file = null)
    {
        // Was there a file provided? 
        if (is_null($file)) {
            // There wasn't, so default it
            $file = $this->_file;
        }

        // Is the file a directory?
        if (!$file->isDir()) {
            // It isn't a directory, so just return it
            return [
                $file,
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

    /**
     * Derives the metadata based on the file.
     *
     * @param SplFileInfo $file (description)
     */
    private function deriveFileMetadata(SplFileInfo $file)
    {
        $this->_isJavaScript |= in_array($file->getExtension(), ['js', 'coffee']);
        $this->_isStyleSheet |= in_array($file->getExtension(), ['css', 'less', 'scss']);
    }
}
