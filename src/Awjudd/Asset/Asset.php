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

    /**
     * Whether or not processing is enabled for the packages.
     * 
     * @var boolean
     */
    private $processingEnabled = FALSE;

    /**
     * The list of files that are being processed by the asset handler.
     * 
     * @var array
     */
    private $files = [];

    public function __construct()
    {
        // Determine if we should process the files
        $this->deriveProcessingEnabled();

        // Set up all of the libraries that we need
        $this->setupLibraries();
    }

    /**
     * Used in order to add a file to the asset management system.
     * 
     * @param string $name
     * @param string $filename
     */
    public function add($name, $filename)
    {
        // Check if the file exists
        if(!file_exists($filename))
        {
            // The file doesn't exist, so throw an exception
            throw new \Exception(\Lang::get('asset::errors.file-not-found', ['file' => $filename]));
        }

        // Grab the file information
        $file = new \SplFileInfo($filename);

        // Check if the file is a directory
        if($file->isDir())
        {
            // It was a directory, so iterate through it
            $directory = new \DirectoryIterator($filename);

            foreach ($directory as $file)
            {
                // Recursively call the add function
                $this->add($name . $file->getFilename(), $file->getRealPath());
            }
        }
        else
        {
            // Check if there is a file extension mapped
            if(isset($this->extensionMapping[$file->getExtension()]))
            {
                $file_to_process = $file->getRealPath();

                // Keep track of the actual asset type that is being processed
                $assetType = NULL;

                // There was something linked to it, so iterate through
                foreach($this->extensionMapping[$file->getExtension()] as $processor)
                {
                    // Check the processor type to make sure we aren't classifying
                    // it as two different types
                    if($assetType !== NULL && $this->processors[$processor]->getAssetType() != $assetType)
                    {
                        // There is a mismatch, so throw an exception
                        throw new \Exception(\Lang::get('asset::errors.different-asset-types', ['file' => $file_to_process]));
                    }
                    // Was there a duplicate name, and we are erroring
                    else if(isset($this->files[$assetType][$name]) && \Config::get('asset::file.error-on-duplicate-name', FALSE))
                    {
                        // We are erroring because of the duplicate name, so throw an exception
                        throw new \Exception(\Lang::get('asset::errors.duplicate-name', ['name' => $name]));
                    }

                    // Process the file
                    $file_to_process = $this->processors[$processor]->process($file_to_process);

                    // Set the asset type
                    $assetType = $this->processors[$processor]->getAssetType();
                }

                // Add it to the list of files that we processed
                $this->files[$assetType][$name] = $file_to_process;
            }
        }
    }

    /**
     * Whether or not asset processing is enabled.
     * 
     * @return boolean
     */
    public function getProcessingEnabled()
    {
        return $this->processingEnabled;
    }

    /**
     * Used internally in order to determine whether or not we need to actually
     * process the input files.
     */
    private function deriveProcessingEnabled()
    {
        // Are they forcing it to be enabled?
        if(\Config::get('asset::enabled.force', FALSE))
        {
            // It was forced, so enable it
            $this->processingEnabled = TRUE;
        }
        else
        {
            // Otherwise derive it based on the environment that we are in
            $this->processingEnabled = in_array(\App::environment()
                    , \Config::get('asset::enabled.environments', array()));
        }
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
            $instance = $class::getInstance($this->processingEnabled);

            // Ensure that it implements the "Awjudd\Asset\Interfaces\IAssetProcessor"
            // interface
            if(!($instance instanceof \Awjudd\Asset\Interfaces\IAssetProcessor))
            {
                throw new \Exception(\Lang::get('asset:errors.invalid-type', ['class' => $class, 'interface' => 'Awjudd\Asset\Interfaces\IAssetProcessor']));
            }

            // Add it into the array of processors
            $this->processors[$name] = $instance;

            // Grab the list of extensions it processes
            $this->deriveExtensionMapping($name, $class::getAssociatedExtensions());

            // Check if the asset type exists
            if(!isset($this->files[$instance->getAssetType()]))
            {
                // Asset type didn't exist, so add it into the mapping
                $this->files[$instance->getAssetType()] = [];
            }
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