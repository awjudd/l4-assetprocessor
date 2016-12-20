<?php

namespace Awjudd\AssetProcessor\Processors\JavaScript;

use CoffeeScript\Compiler;
use Awjudd\AssetProcessor\Asset\Asset;
use Awjudd\AssetProcessor\Processors\BaseProcessor;

class CoffeeScriptProcessor extends BaseProcessor
{
    /**
     * Retrieves all of the extensions that this processor handles.
     *
     * @var array
     */
    public function getExtensions()
    {
        return [
            'coffee',
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
        return 'coffee-script';
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
        return $this->write($asset, Compiler::compile($this->read($asset)));
    }

    /**
     * Should this processor always be run?
     *
     * @return     boolean
     */
    public function alwaysProcess()
    {
        return true;
    }
}
