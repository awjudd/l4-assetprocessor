<?php

namespace Awjudd\AssetProcessor\Processors\Common;

use Awjudd\AssetProcessor\Asset\Asset;
use Awjudd\AssetProcessor\Processors\BaseProcessor;

class FinalProcessor extends BaseProcessor
{
    /**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var array
     */
    public function getExtensions()
    {
        return [
            'less', 'scss', 'css', 'js', 'coffee',
        ];
    }

    /**
     * Get the target file extension.
     *
     * @return     string  Target file extension.
     */
    public function getTargetExtension(Asset $asset)
    {
        return in_array($asset->getExtension(), ['js', 'coffee']) ? 'js' : 'css';
    }

    /**
     * Retrieves the alias for the asset processor.
     *
     * @return string Alias.
     */
    public function getAlias()
    {
        return 'final';
    }

    /**
     * Processes the asset.
     *
     * @param Asset $asset The asset to process
     * 
     * @return Asset
     */
    public function process(Asset $asset)
    {
        return $this->write($asset, $this->read($asset));
    }
}