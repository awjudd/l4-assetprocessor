<?php namespace Awjudd\Asset\Processors;


class LessCSSProcessor extends BaseProcessor
{
    /**
     * An array containing all of the file extensions that this processor needs
     * to use.
     * 
     * @var array
     */
    public static $extensions = ['less'];

    /**
     * The type of processor this instance is.
     * 
     * @return string
     */
    public static function getType()
    {
        return 'LESS CSS Processor';
    }

    /**
     * The description of this processor.
     * 
     * @var string
     */
    public static function getDescription()
    {
        return 'Used in order to process any of the provided LESS files.';
    }

    /**
     * Determines the classification of an asset.
     * 
     * @return string
     */
    public static function getAssetType()
    {
        return 'css';
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

        $less = new \lessc();

        return $this->write($less->compileFile($filename), $filename);
    }

    /**
     * Whether or not we should bypass the process filter
     * 
     * @return boolean
     */
    public function bypassProcess()
    {
        return true;
    }
}