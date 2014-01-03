<?php namespace Awjudd\AssetProcessor\Processors;


class ScssCSSProcessor extends BaseProcessor
{
    /**
     * An array containing all of the file extensions that this processor needs
     * to use.
     * 
     * @var array
     */
    public static $extensions = ['scss'];

    /**
     * The type of processor this instance is.
     * 
     * @return string
     */
    public static function getType()
    {
        return 'SCSS CSS Processor';
    }

    /**
     * The description of this processor.
     * 
     * @var string
     */
    public static function getDescription()
    {
        return 'Used in order to process any of the provided SCSS files.';
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
     * @param string $actualFileName
     * @return string
     */
    public function process($filename, $actualFileName)
    {
        // Check if the processing should be done
        if(!$this->shouldProcess($filename))
        {
            // No need to process the file, so bypass
            return $this->getFinalName($filename);
        }

        // Get an instance of the SASS compiler
        $sass = new \scssc();

        // Otherwise write the file and return the new file name
        return $this->write($sass->compile(file_get_contents($filename)), $actualFileName);
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