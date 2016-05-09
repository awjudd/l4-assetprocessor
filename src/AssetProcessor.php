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
    public function cdn($asset)
    {
        // Is there a CDN group?
        if (!isset($this->_groups['cdn'])) {
            // There isn't, so make it
            $this->_groups['cdn'] = new AssetGroup('cdn', AssetGroup::CDN);
        }

        // Add in the asset
        $this->_groups['cdn']->add($asset);
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
            if ($group->groupRetrieved('styles')) {
                $body .= $group->stylesheet();
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
            if ($group->groupRetrieved('styles')) {
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
            return array_keys($this->_groups);
        } else {
            return [
                $group,
            ];
        }
    }
}
