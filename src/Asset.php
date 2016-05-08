<?php

namespace Awjudd\AssetProcessor;

use SplFileInfo;
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
        $this->_isCdn = $isCdn;

        // Fill in the file metadata
        $this->deriveFileMetadata();
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
        return $this->_isStyleSheet;
    }

    /**
     * Determines if the asset is a JavaScript file
     *
     * @return     boolean  True if JavaScript file, False otherwise.
     */
    public function isJavaScript()
    {
        return $this->_isJavaScript;
    }

    /**
     * Retrieves the HTML which is related to this asset.
     *
     * @param      array   $attributes  Any extra attributes which are required
     *
     * @return     string
     */
    public function get(array $attributes = [])
    {
        return $this->isStylesheet() ? $this->stylesheet($attributes) : $this->javascript($attributes);
    }

    /**
     * Retrieves the extension of the file
     *
     * @return     string  Extension.
     */
    public function getExtension()
    {
        return $this->_file->getExtension();
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
    private function stylesheet(array $attributes)
    {
        return sprintf(
            '<link rel="stylesheet" type="text/css" href="%s" %s>',
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
    private function javascript(array $attributes)
    {
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
                    '%s="%s"',
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
    private function deriveFileMetadata()
    {
        $this->_isJavaScript = in_array($this->getExtension(), ['js', 'coffee']);
        $this->_isStyleSheet = in_array($this->getExtension(), ['css', 'less', 'scss']);
    }
}