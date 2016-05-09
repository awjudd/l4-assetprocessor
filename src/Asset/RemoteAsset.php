<?php

namespace Awjudd\AssetProcessor\Asset;

class RemoteAsset extends Asset
{
    /**
     * Instantiates the asset object.
     *
     * @param string $url The URL to the file that we are processing.
     */
    private function __construct($url)
    {
        $this->_url = $url;

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
        return [
            new RemoteAsset($path)
        ];
    }

    /**
     * Retrieves the name of the asset file.
     *
     * @return     string  Name.
     */
    public function getName()
    {
        return basename($this->_url);
    }

    /**
     * Processes the asset.
     * 
     * @return Asset The updated asset object
     */
    public function process()
    {
        // Return the helper
        return $this;
    }

    /**
     * Retrieves the public path for the asset.
     *
     * @return string Public path.
     */
    public function getPublicPath()
    {
        // Return the file name
        return $this->_url;
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
        return [];
    }

    /**
     * Derives the metadata that is required for the asset.
     */
    protected function deriveMetadata()
    {
        // We are, so just look at the filename
        $basename = basename($this->_url);
        $extension = substr($basename, strripos($basename, '.') + 1);

        $this->_isJavaScript = in_array($extension, ['js', 'coffee']);
        $this->_isStyleSheet = in_array($extension, ['css', 'less', 'scss']);
    }
}
