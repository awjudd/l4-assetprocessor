<?php

namespace Awjudd\AssetProcessor\Asset;

class RemoteAsset extends Asset
{
    /**
     * The full URL to the asset.
     * 
     * @var string
     */
    private $_url;

    /**
     * The base information of the asset.
     * 
     * @var string
     */
    private $_basename;

    /**
     * Instantiates the asset object.
     *
     * @param string $url The URL to the file that we are processing.
     */
    private function __construct($url)
    {
        $this->_url = $url;

        // We are, so just look at the filename
        $this->_basename = basename($this->_url);

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
            new self($path),
        ];
    }

    /**
     * Retrieves the name of the asset file.
     *
     * @return string Name.
     */
    public function getName()
    {
        return $this->_basename;
    }

    /**
     * Retrieves the file extension for the asset.
     * 
     * @return string
     */
    public function getExtension()
    {
        return substr($this->_basename, strripos($this->_basename, '.') + 1);
    }

    /**
     * Retrieves the file's full name.
     * 
     * @return string
     */
    public function getFullName()
    {
        return $this->_url;
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
     * Derives the metadata that is required for the asset.
     */
    protected function deriveMetadata()
    {
        $extension = $this->getExtension();

        $this->_isJavaScript = in_array($extension, ['js', 'coffee']);
        $this->_isStyleSheet = in_array($extension, ['css', 'less', 'scss']);
    }
}
