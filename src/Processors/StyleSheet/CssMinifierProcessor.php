<?php

namespace Awjudd\AssetProcessor\Processors\StyleSheet;

use CssMinifier;
use Awjudd\AssetProcessor\Asset\Asset;
use Awjudd\AssetProcessor\Processors\BaseProcessor;

class CssMinifierProcessor extends BaseProcessor
{
    /**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var array
     */
    public function getExtensions()
    {
        return [
            'less', 'scss', 'css',
        ];
    }

    /**
     * Get the target file extension.
     *
     * @return     string  Target file extension.
     */
    public function getTargetExtension(Asset $asset)
    {
        return 'css';
    }

    /**
     * Retrieves the alias for the asset processor.
     *
     * @return string Alias.
     */
    public function getAlias()
    {
        return 'css-minifier';
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
        $css = new CssMinifier($this->read($asset));

        return $this->write($asset, $css->getMinified());
    }
}