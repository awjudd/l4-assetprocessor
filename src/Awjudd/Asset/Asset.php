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
            $this->processors[$name] = $class::getInstance();
        }
    }
}