<?php

namespace Awjudd\AssetProcessor;

use Awjudd\AssetProcessor\Processor\Processor;

class Asset
{
    /**
     * The name of the file that will be processed
     * 
     * @var        string
     */
    private $_filename;

    /**
     * Whether or not the asset is a CDN asset.
     * 
     * @var        boolean
     */
    private $_isCdn;

    /**
     * Instantiates the asset object
     *
     * @param      string   $filename  The name of the file we will be processing.
     * @param      boolean  $isCdn     Whether or not the asset is a CDN asset.
     */
    public function __construct($filename, $isCdn)
    {
        $this->_filename = $filename;
        $this->_isCdn = $isCdn;
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
        return false;
    }

    /**
     * Determines if the asset is a JavaScript file
     *
     * @return     boolean  True if JavaScript file, False otherwise.
     */
    public function isJavaScript()
    {
        return false;
    }

    public function get()
    {

    }
}