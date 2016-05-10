<?php

namespace Awjudd\AssetProcessor;

class AssetProcessor
{
    /**
     * All of the asset groups to use.
     *
     * @var array
     */
    private $_groups = [];

    /**
     * Adds in a CDN asset.
     *
     * @param string $asset The URL to the asset
     */
    public function remote($asset, $group = 'cdn')
    {
        // Is there a CDN group?
        if (!isset($this->_groups[$group])) {
            // There isn't, so make it
            $this->_groups[$group] = new AssetGroup($group, AssetGroup::REMOTE);
        }

        // Add in the asset
        $this->_groups[$group]->add($asset);
    }

    /**
     * Adds an asset to the asset list.
     *
     * @param string $name  A unique asset name
     * @param string $file  (description)
     * @param string $group (description)
     */
    public function add($name, $file, $group = 'default')
    {
        // Is there a group already defined?
        if (!isset($this->_groups[$group])) {
            // There isn't, so make it
            $this->_groups[$group] = new AssetGroup($group);
        }

        // Add the asset in
        $this->_groups[$group]->add($file, $name);
    }

    /**
     * Retrieves the base storage path for assets.
     *
     * @param      string  $path   (description)
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function storage_path($path = '')
    {
        return realpath('../storage/'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }

    /**
     * Retrieves the base resource path for assets
     *
     * @param      string  $path   (description)
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function resource_path($path = '')
    {
        return realpath('../resources/'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }

    /**
     * Retrieves the styles for the specified.
     *
     * @param string $group (description)
     *
     * @return string ( description_of_the_return_value )
     */
    public function styles($group = null)
    {
        $body = '';

        $groups = $this->getGroups($group);

        foreach ($groups as $group) {
            if (!$group->isRetrieved('styles')) {
                $body .= $group->styles();
            }
        }

        return $body;
    }

    /**
     * Retrieves the styles for the specified.
     *
     * @param string $group (description)
     *
     * @return string ( description_of_the_return_value )
     */
    public function scripts($group = null)
    {
        $body = '';

        $groups = $this->getGroups($group);

        foreach ($groups as $group) {
            if (!$group->isRetrieved('styles')) {
                $body .= $group->scripts();
            }
        }

        return $body;
    }

    /**
     * Get the list of groups to process.
     *
     * @param string $group The asset group we want to look for
     *
     * @return array Asset Groups.
     */
    private function getGroups($group = null)
    {
        // Did they provide a group?
        if (is_null($group)) {
            // They didn't, so add all
            return $this->_groups;
        } else {
            return [
                $this->_groups[$group],
            ];
        }
    }
}
