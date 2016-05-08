<?php

namespace Awjudd\AssetProcessor;

use SplFileInfo;
use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Awjudd\AssetProcessor\Processor\Processor;

class Asset
{
    /**
     * The name of the file that will be processed
     * 
     * @var        SplFileInfo
     */
    private $_file;

    /**
     * Whether or not the asset is a CDN asset.
     * 
     * @var        boolean
     */
    private $_isCdn = false;

    /**
     * Whether or not the file is JavaScript
     *
     * @var        boolean
     */
    private $_isJavaScript = false;

    /**
     * Whether or not the file is a StyleSheet
     *
     * @var        boolean
     */
    private $_isStyleSheet = false;

    /**
     * Instantiates the asset object
     *
     * @param      string   $filename  The name of the file we will be processing.
     * @param      boolean  $isCdn     Whether or not the asset is a CDN asset.
     */
    public function __construct($filename, $isCdn)
    {
        $this->_file = new SplFileInfo($filename);

        // Is the file valid?
        if(!$this->_file->isFile() && !$this->_file->isDir()) {
            // The user didn't provide a single file, so we can't handle it
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid file provided (%s)', $filename
                )
            );
        }

        $this->_isCdn = $isCdn;

        // Fill in the file metadata
        $this->deriveMetadata();
    }

    /**
     * Processes the asset.
     * 
     * @return     Asset The updated asset object
     */
    public function process()
    {
        // Is the asset a CDN?
        if($this->_isCdn) {
            // If the asset is on a CDN, then we don't need to worry about it, so we are done
            return $this;
        }

        // Return the helper
        return Processor::process($this);
    }

    /**
     * Determines if the processor handles style sheet
     *
     * @return     boolean  True if style sheet processor, False otherwise.
     */
    public function isStylesheet()
    {
        return (bool)$this->_isStyleSheet;
    }

    /**
     * Determines if the asset is a JavaScript file
     *
     * @return     boolean  True if JavaScript file, False otherwise.
     */
    public function isJavaScript()
    {
        return (bool)$this->_isJavaScript;
    }

    /**
     * Retrieves the public path for the asset
     *
     * @return     string  Public path.
     */
    public function getPublicPath()
    {
        return '';
    }

    /**
     * Retrieves the HTML required for a stylesheet
     *
     * @param      array  $attributes  Any extra attributes to provide
     *
     * @return     string  The HTML to emit
     */
    public function stylesheet(array $attributes = [])
    {
        // Are we a stylesheet?
        if(!$this->isStylesheet()) {
            // We aren't, so we are done
            return '';
        }

        return sprintf(
            '<link rel="stylesheet" type="text/css" href="%s" %s />',
            $this->getPublicPath(),
            $this->deriveAttributes($attributes)
        );
    }

    /**
     * Retrieves the HTML required for a JavaScript
     *
     * @param      array  $attributes  Any extra attributes to provide
     *
     * @return     string  The HTML to emit
     */
    public function javascript(array $attributes = [])
    {
        // Are we a JavaScript file?
        if(!$this->isJavaScript()) {
            // We aren't, so we are done
            return '';
        }

        return sprintf(
            '<script type="text/javascript" src="%s" %s></script>',
            $this->getPublicPath(),
            $this->deriveAttributes($attributes)
        );
    }

    /**
     * Derives the key-value pair of attributes.
     *
     * @param      array   $attributes  The attributes to include
     *                                  
     * @return     string  The HTML to emit for any attributes.
     */
    private function deriveAttributes(array $attributes)
    {
        $text = '';

        // Loop through any attributes
        foreach($attributes as $key => $value) {
            $text .= sprintf(
                    '%s="%s" ',
                    $key,
                    htmlentities($value)
                );
        }

        return $text;
    }

    /**
     * Derives the metadata that is required for the asset.
     * 
     * @return void
     */
    private function deriveMetadata()
    {
        $files = $this->getFiles();

        foreach($files as $file) {
            $this->deriveFileMetadata($file);
        }
    }

    /**
     * Retrieves all of the files that are being handled by the asset.
     *
     * @param      SplFileInfo  $file   The current file
     *
     * @return     array        Files.
     */
    private function getFiles(SplFileInfo $file = null)
    {
        // Was there a file provided? 
        if(is_null($file)) {
            // There wasn't, so default it
            $file = $this->_file;
        }

        // Is the file a directory?
        if(!$file->isDir()) {
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

        foreach($iterator as $filename => $info) {
            // Make sure it's not pointing at itself
            if(in_array($info->getFilename(), ['.', '..'])) {
                continue;
            }

            // Add the files to the end of the array
            $files[] = $info;
        }

        // Return the files
        return $files;
    }

    /**
     * Derives the metadata based on the file
     *
     * @param      SplFileInfo  $file   (description)
     */
    private function deriveFileMetadata(SplFileInfo $file)
    {
        $this->_isJavaScript |= in_array($file->getExtension(), ['js', 'coffee']);
        $this->_isStyleSheet |= in_array($file->getExtension(), ['css', 'less', 'scss']);
    }
}