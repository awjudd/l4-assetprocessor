<?php

namespace Awjudd\AssetProcessor;

use Awjudd\AssetProcessor\Asset;

class AssetGroup
{
    const CDN = 'cdn';
    const INTERNAL = 'internal';

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
     */
    public function __construct($name, $type)
    {
        $this->_name = $name;
        $this->_type = $type;
    }

    /**
     * Adds a new file to the asset group.
     *
     * @param      string  $filename  The full path to the file
     */
    public function add($filename)
    {
        $this->_assets[] = new Asset($filename, $this->_type == self::CDN);
    }
}