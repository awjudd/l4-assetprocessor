<?php

namespace Awjudd\AssetProcessor\Processors\StyleSheet;

use Leafo\ScssPhp\Compiler;
use Awjudd\AssetProcessor\Asset\Asset;
use Awjudd\AssetProcessor\Processors\BaseProcessor;

class ScssProcessor extends BaseProcessor
{
	/**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var array
     */
    public function getExtensions()
    {
        return [
            'scss',
        ];
    }

    /**
     * Get the target file extension.
     *
     * @return string Target file extension.
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
        return 'scss';
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
        $scssc = new Compiler();

        return $this->write($asset, $scssc->compile($this->read($asset)));
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