<?php namespace Awjudd\Asset;

use Awjudd\Asset\Processors\JsMinifierProcessor as JsMinifier;

class Asset
{
    /**
     * The processors that will be used for the rest of the work.
     * 
     * @var array
     */
    protected $processors = [];

    /**
     * An array that will map all of the associated extensions to a specific
     * processor.
     * 
     * @var array
     */
    private $extensionMapping = [];

    public function __construct()
    {
        // Set up all of the libraries that we need
        $this->setupLibraries();
    }


    public function add($name, $file)
    {

    }

    /**
     * Used internally in order to set up an instance of each of the processors
     * that we will need.
     */
    private function setupLibraries()
    {
        // Get the list of processors
        $processors = \Config::get('asset::processors', []);

        // Iterate through the list
        foreach($processors as $name => $class)
        {
            // Get an instance of the processor
            $instance = $class::getInstance();

            // Ensure that it implements the "Awjudd\Asset\Interfaces\IAssetProcessor"
            // interface
            if(!($instance instanceof \Awjudd\Asset\Interfaces\IAssetProcessor))
            {
                throw new \Exception('Invalid asset class provided (' . $class . ') must implement "Awjudd\Asset\Interfaces\IAssetProcessor".');
            }

            // Grab the list of extensions it processes
            $this->deriveExtensionMapping($name, $class::getAssociatedExtensions());
        }
    }

    /**
     * Used internally in order to derive the list of extensions that are used
     * by a particular processor.
     */
    private function deriveExtensionMapping($name, array $extensions)
    {
        // Iterate through all of the extensions
        foreach($extensions as $extension)
        {
            // Check if it exists
            if(!isset($this->extensionMapping[$extension]))
            {
                // It doesn't, so initialize it
                $this->extensionMapping[$extension] = [];
            }

            // Add a mapping between the two
            $this->extensionMapping[$extension][] = $name;
        }
    }
}