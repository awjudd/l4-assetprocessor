<?php

namespace Awjudd\AssetProcessor\Processors\JavaScript;

use Tholu\Packer\Packer;
use Awjudd\AssetProcessor\Asset\Asset;
use Awjudd\AssetProcessor\Processors\BaseProcessor;

class JavaScriptPackerProcessor extends BaseProcessor
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
        return 'js-packer';
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
        $packer = new Packer($this->read($asset), 'Normal', true, false, true);
        return $this->write($asset, $packer->pack());
    }
}
