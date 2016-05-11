<?php

namespace Awjudd\AssetProcessor\Processors\JavaScript;

use JsMin\Minify;
use Awjudd\AssetProcessor\Asset\Asset;
use Awjudd\AssetProcessor\Processors\BaseProcessor;

class JavaScriptMinifierProcessor extends BaseProcessor
{
    /**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var array
     */
    public function getExtensions()
    {
        return [
            'js', 'coffee',
        ];
    }

    /**
     * Get the target file extension.
     *
     * @return string Target file extension.
     */
    public function getTargetExtension(Asset $asset)
    {
        return 'js';
    }

    /**
     * Retrieves the alias for the asset processor.
     *
     * @return string Alias.
     */
    public function getAlias()
    {
        return 'js-minifier';
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
        return $this->write($asset, Minify::minify($this->read($asset)));
    }
}
