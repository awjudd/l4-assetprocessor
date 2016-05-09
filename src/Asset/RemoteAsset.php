<?php

namespace Awjudd\AssetProcessor\Asset;

use Awjudd\AssetProcessor\Asset\Asset;

class RemoteAsset extends Asset
{
    /**
     * Instantiates the asset object
     *
     * @param      string   $url  The URL to the file that we are processing.
     */
    public function __construct($url)
    {
        $this->_url = $url;

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
        // Return the helper
        return $this;
    }

    /**
     * Retrieves the public path for the asset
     *
     * @return     string  Public path.
     */
    public function getPublicPath()
    {
        // Return the file name
        return $this->_url;
    }

    /**
     * Derives the metadata that is required for the asset.
     * 
     * @return void
     */
    protected function deriveMetadata()
    {
        // We are, so just look at the filename
        $extension = substr($this->_url, strripos($this->_url, '.') + 1);

        $this->_isJavaScript = in_array($extension, ['js', 'coffee']);
        $this->_isStyleSheet = in_array($extension, ['css', 'less', 'scss']);
    }
}