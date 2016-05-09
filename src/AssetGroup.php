<?php

namespace Awjudd\AssetProcessor;

use InvalidArgumentException;
use Awjudd\AssetProcessor\Asset\LocalAsset;
use Awjudd\AssetProcessor\Asset\RemoteAsset;

class AssetGroup
{
    const REMOTE = 'remote';
    const INTERNAL = 'internal';

    /**
     * The name of the asset group.
     * 
     * @var string
     */
    private $_name;

    /**
     * The type of the asset group.
     * 
     * @var string
     */
    private $_type;

    /**
     * All of the assets which have been added to the group.
     *
     * @var array
     */
    private $_assets = [];

    private $_retrieved = [
        'styles' => false,
        'scripts' => false,
    ];

    /**
     * Instantiates the asset group.
     *
     * @param string $name The name of the asset group
     * @param string $type The type of assets which we will be serving
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name, $type = self::INTERNAL)
    {
        // Ensure that the asset group type is valid
        if (!in_array($type, [self::REMOTE, self::INTERNAL])) {
            // Throw an exception as it is not.
            throw new InvalidArgumentException('The asset group type is invalid.');
        }

        $this->_name = $name;
        $this->_type = $type;
    }

    /**
     * Determines whether or not an asset group is for remote assets.
     *
     * @return bool True if remote assets, False otherwise.
     */
    public function isRemote()
    {
        return $this->_type == self::REMOTE;
    }

    /**
     * Adds a new file to the asset group.
     *
     * @param string $filename The full path to the file
     * @param string $name     The name of the asset
     */
    public function add($filename, $name = null)
    {
        // Was the name provided?
        if (is_null($name)) {
            // It wasn't, so assign it to the filename
            $name = $filename;
        }

        // Build the asset
        $assets = $this->isRemote() ? RemoteAsset::create($filename) : LocalAsset::create($filename);

        foreach($assets as $asset) {
            $this->_assets[sprintf(
                '%s-%s', $filename, $asset->getName()
            )] = $asset;
        }
    }

    /**
     * Returns the complete list of assets.
     *
     * @return array Assets.
     */
    public function getAssets()
    {
        return $this->_assets;
    }

    /**
     * Retrieves all of the scripts related to the this asset group.
     *
     * @param array $attributes (description)
     */
    public function scripts(array $attributes = [])
    {
        $body = '';

        // Was there a "asset-group" attribute?
        if (!isset($attributes['asset-group'])) {
            $attributes['asset-group'] = $this->_name;
        }

        foreach ($this->_assets as $asset) {
            $body .= $asset->javascript($attributes);
        }

        // Mark the asset group as retrieved
        $this->_retrieved['scripts'] = true;

        return $body;
    }

    /**
     * Retrieves all of the styles related to the this asset group.
     *
     * @param array $attributes (description)
     */
    public function styles(array $attributes = [])
    {
        $body = '';

        // Was there a "asset-group" attribute?
        if (!isset($attributes['asset-group'])) {
            $attributes['asset-group'] = $this->_name;
        }

        // Build the list of assets
        foreach ($this->_assets as $asset) {
            $body .= $asset->stylesheet($attributes);
        }

        // Mark the asset group as retrieved
        $this->_retrieved['styles'] = true;

        return $body;
    }

    /**
     * Determines whether or not the specific type is.
     *
     * @param string $type (description)
     *
     * @return bool
     */
    public function isRetrieved($type)
    {
        if (!isset($this->_retrieved[$type])) {
            return false;
        }

        return $this->_retrieved[$type];
    }
}
