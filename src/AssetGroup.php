<?php

namespace Awjudd\AssetProcessor;

use Config;
use InvalidArgumentException;
use Awjudd\AssetProcessor\Asset;

class AssetGroup
{
    const CDN = 'cdn';
    const INTERNAL = 'internal';

    private $_config;

    /**
     * The name of the asset group.
     * 
     * @var        string
     */
    private $_name;

    /**
     * The type of the asset group.
     * 
     * @var        string
     */
    private $_type;

    /**
     * All of the assets which have been added to the group.
     *
     * @var        array
     */
    private $_assets = [];

    /**
     * Instantiates the asset group.
     *
     * @param      string  $name   The name of the asset group
     * @param      string  $type   The type of assets which we will be serving
     * @throws     InvalidArgumentException
     */
    public function __construct($name, $type = self::INTERNAL)
    {
        // Ensure that the asset group type is valid
        if(!in_array($type, [self::CDN, self::INTERNAL])) {
            // Throw an exception as it is not.
            throw new InvalidArgumentException('The asset group type is invalid.');
        }

        $this->_name = $name;
        $this->_type = $type;
    }

    /**
     * Determines whether or not an asset group is for CDNs
     *
     * @return     boolean  True if cdn, False otherwise.
     */
    public function isCdn()
    {
        return $this->_type == self::CDN;
    }

    /**
     * Adds a new file to the asset group.
     *
     * @param      string  $filename  The full path to the file
     */
    public function add($filename)
    {
        // Build the asset
        $asset = new Asset($filename, $this->isCdn());

        // Do we want to allow mixed asset groups?
        /*if(!Config::get('asset-processor.checks.allow-mixed-asset-groups')) {
            // We don't, so verify that 
        }*/

        $this->_assets[] = $asset;
    }

    /**
     * Returns the complete list of assets.
     *
     * @return     array  Assets.
     */
    public function getAssets()
    {
        return $this->_assets;
    }
}