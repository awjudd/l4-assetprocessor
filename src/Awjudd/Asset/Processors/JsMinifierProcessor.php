<?php namespace Awjudd\Asset\Processors;

use JsMin\Minify;

class JsMinifierProcessor extends BaseProcessor
{
    /**
     * An array containing all of the file extensions that this processor needs
     * to use.
     * 
     * @var array
     */
    public static $extensions = ['js', 'coffee'];

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
     * Determines the classification of an asset.
     * 
     * @return string
     */
    public static function getAssetType()
    {
        return 'js';
    }

    /**
     * Used in order to process the input file.  After processing this input
     * file, it will return a new file name for the rest of the process to use
     * if needed.
     * 
     * @param string $filename
     * @return string
     */
    public function process($filename)
    {
        if(!$this->shouldProcess($filename))
        {
            return $this->getFinalName($filename);
        }

        // Read the contents of the JavaScript file
        $js = file_get_contents($filename);

        // Minify the JavaScript and then write it out
        return $this->write(Minify::minify($js), $filename);
    }

    /**
     * Whether or not we should bypass the process filter
     * 
     * @return boolean
     */
    public function bypassProcess()
    {
        return false;
    }
}