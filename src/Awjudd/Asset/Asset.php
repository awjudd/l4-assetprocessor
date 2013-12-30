<?php namespace Awjudd\Asset;

use Awjudd\Asset\Processors\JsMinifierProcessor as JsMinifier;

class Asset
{
    /**
     * Holds the instance of Lessc used to process .less files
     * 
     * @var lessc
     */
    public $lessc = NULL;

    /**
     * Holds the instance of a JavaScript minifier.
     * 
     * @var jsMin
     */
    public $jsMin = NULL;

    public function __construct()
    {
        // Set up all of the libraries that we need
        $this->setupLibraries();
    }

    private function setupLibraries()
    {
        $foo = JsMinifier::getInstance();
    }
}