<?php

namespace Awjudd\AssetProcessor\Processors\Common;

use Awjudd\AssetProcessor\Asset\Asset;
use Awjudd\AssetProcessor\Processors\BaseProcessor;

class FinalOutputProcessor extends BaseProcessor
{
    /**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var array
     */
    public function getExtensions()
    {
        return [
            'css',
            'js',
        ];
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
    }
}
