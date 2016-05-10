<?php

namespace Awjudd\AssetProcessor\Processors\StyleSheet;

use lessc;
use Awjudd\AssetProcessor\Processors\BaseProcessor;

class LessCssProcessor extends BaseProcessor
{
    /**
     * Retrieves all of the extensions that this processor handles.
     * 
     * @var array
     */
    public function getExtensions()
    {
        return [
            'less',
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
        return 'less';
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
        $less = new lessc();
        return $this->write($asset, $less->compile($this->read($asset)));
    }
}