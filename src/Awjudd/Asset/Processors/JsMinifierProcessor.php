<?php namespace Awjudd\Asset\Processors;

use Awjudd\Asset\Interfaces\IAssetProcessor;

class JsMinifierProcessor extends BaseProcessor
{
    /**
     * The type of processor this instance is.
     * 
     * @return string
     */
    public static function getType()
    {
        return 'JavaScript Minifier';
    }

    /**
     * The description of this processor.
     * 
     * @var string
     */
    public static function getDescription()
    {
        return 'Used in order to minimize all of the JavaScript files that are provided.';
    }

    /**
     * Used to retrieve all of the assets processed through this object.
     * 
     * @return array Full paths to each of the assets
     */
    public function retrieve()
    {
        
    }
}