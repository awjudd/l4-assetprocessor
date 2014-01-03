<?php namespace Awjudd\AssetProcessor;

class AssetProcessor
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
    private $processingEnabled = false;

    /**
     * The list of files that are being processed by the asset handler.
     * 
     * @var array
     */
    private $files = [];

    /**
     * Returns the base storage folder for any files.
     * 
     * @return string
     */
    public static function storageFolder()
    {
        return storage_path() . '/' . \Config::get('assetprocessor::cache.directory') . '/';
    }


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
     * @return void
     * @throws \Exception
     */
    public function add($name, $filename)
    {
        // Check if the file exists
        if(!file_exists($filename))
        {
            // The file doesn't exist, so throw an exception
            throw new \Exception(\Lang::get('assetprocessor::errors.asset.file-not-found', ['file' => $filename]));
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
                // Make sure we aren't the dot directory
                if(!$file->isDot())
                {
                    // Recursively call the add function
                    $this->add($name . $file->getFilename(), $file->getRealPath());
                }
            }
        }
        else
        {
            // Check if there is a file extension mapped
            if(isset($this->extensionMapping[$file->getExtension()]))
            {
                $file_to_process = $file->getRealPath();

                // Keep track of the actual asset type that is being processed
                $assetType = null;

                // There was something linked to it, so iterate through
                foreach($this->extensionMapping[$file->getExtension()] as $processor)
                {
                    // Check the processor type to make sure we aren't classifying
                    // it as two different types
                    if($assetType !== null && $this->processors[$processor]->getAssetType() != $assetType)
                    {
                        // There is a mismatch, so throw an exception
                        throw new \Exception(\Lang::get('assetprocessor::errors.asset.different-asset-types', ['file' => $file_to_process]));
                    }
                    // Was there a duplicate name, and we are erroring
                    else if(isset($this->files[$assetType][$name]) && \Config::get('assetprocessor::file.error-on-duplicate-name', false))
                    {
                        // We are erroring because of the duplicate name, so throw an exception
                        throw new \Exception(\Lang::get('assetprocessor::errors.asset.duplicate-name', ['name' => $name]));
                    }

                    // Check if we should be processing the file
                    if($this->processingEnabled || $this->processors[$processor]->bypassProcess())
                    {
                        // It is so, process the file
                        $file_to_process = $this->processors[$processor]->process($file_to_process, $file->getFileName());
                    }

                    // Set the asset type
                    $assetType = $this->processors[$processor]->getAssetType();
                }

                // Check if the file was processed
                if($file_to_process!=$file->getRealPath())
                {
                    // It was, so add it to the base folder
                    $dest_path = storage_path() . '/' . \Config::get('assetprocessor::cache.directory') . '/' . $assetType . '/' . basename($file_to_process);

                    // Copy the file over
                    copy($file_to_process, $dest_path);

                    // Grab the file name to add to our asset list
                    $file_to_process = basename($dest_path);
                }

                // Add it to the list of files that we processed
                $this->files[$assetType][$name] = $file_to_process;
            }
        }
    }

    /**
     * Used to retrieve a single string which will contain all styles that are
     * needed for your application.
     * 
     * @return string
     */
    public function styles()
    {
        return $this->retrieve('css');
    }

    /**
     * Used to retrieve a single string which will contain all JavaScript that are
     * needed for your application.
     * 
     * @return string
     */
    public function scripts()
    {
        return $this->retrieve('js');
    }

    /**
     * Used internally to retrieve a single string to used to retrieve all of the required assets.
     * 
     * @param string $type
     * @return string
     */
    private function retrieve($type)
    {
        // The string which will be emitted with all of the information
        $output = '';

        // The controller and method that will be used to emit the processed files
        $controller = \Config::get('assetprocessor::controller.name') . '@' . \Config::get('assetprocessor::controller.method');

        // Are we needing a single file?
        if(\Config::get('assetprocessor::cache.singular') && $this->processingEnabled)
        {
            $assets = $this->files[$type];

            // We only want a single file per type, so combine all of them together
            if(count($assets) == 1)
            {
                // There is only one, so just grab it
                $asset = current($assets);

                // Add in a bypass since there is no point in re-processing the file
                switch($type)
                {
                    case 'js':
                        $output .= \HTML::script(\URL::action($controller, array($type, $asset)));
                        break;
                    case 'css':
                        $output .= \HTML::style(\URL::action($controller, array($type, $asset)));
                        break;
                }
            }
            else
            {
                // There was more than one file, so we need to combine them all
                // into one file
                $file = $this->write($type, $assets);

                // Add in a bypass since there is no point in re-processing the file
                switch($type)
                {
                    case 'js':
                        $output .= \HTML::script(\URL::action($controller, array($type, $file)));
                        break;
                    case 'css':
                        $output .= \HTML::style(\URL::action($controller, array($type, $file)));
                        break;
                }
            }
        }
        else
        {
            // We want several files for each, so return each.
            foreach($this->files[$type] as $file)
            {
                // Check if the asset is internal
                if(\Str::contains($file, public_path()))
                {
                    $asset = str_replace(public_path(), '', $file);

                    // It is external, so just emit it
                    // Add in the asset
                    switch($type)
                    {
                        case 'js':
                            $output .= \HTML::script($asset);
                            break;
                        case 'css':
                            $output .= \HTML::style($asset);
                            break;
                    }
                }
                else
                {
                    $actual_name = substr($file, -32);

                    // It is internal, so emit with the asset controller
                    // Add in the asset
                    switch($type)
                    {
                        case 'js':
                            $output .= \HTML::script(\URL::action($controller, array($type, $actual_name)));
                            break;
                        case 'css':
                            $output .= \HTML::style(\URL::action($controller, array($type, $actual_name)));
                            break;
                    }
                }

                
            }
        }

        return $output;
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
        if(\Config::get('assetprocessor::enabled.force', false))
        {
            // It was forced, so enable it
            $this->processingEnabled = true;
        }
        else
        {
            // Otherwise derive it based on the environment that we are in
            $this->processingEnabled = in_array(\App::environment()
                    , \Config::get('assetprocessor::enabled.environments', []));
        }
    }

    /**
     * Used internally in order to set up an instance of each of the processors
     * that we will need.
     * 
     * @return void
     */
    private function setupLibraries()
    {
        // Get the list of processors
        $processors = \Config::get('assetprocessor::processors.types', []);

        // Grab the interface we should be implementing
        $interface = \Config::get('assetprocessor::processors.interface');

        // Iterate through the list
        foreach($processors as $name => $class)
        {
            // Get an instance of the processor
            $instance = $class::getInstance($this->processingEnabled);

            // Ensure that it implements the correct interface
            if(!($instance instanceof $interface))
            {
                throw new \Exception(\Lang::get('assetprocessor::errors.asset.invalid-type', ['class' => $class, 'interface' => $interface]));
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
     * 
     * @return void
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

    /**
     * Used internally in order to write all of the contents for the provided files
     * into a single file.
     * 
     * @param string $type The type of asset that we are making
     * @param array $contents An array of all of the files to combine
     * @return string The new file name
     */
    private function write($type, array $contents)
    {
        // Will contain all of the files put together
        $file = '';

        // Derive the destination path
        $directory =  static::storageFolder() . $type . '/';

        // Cycle through each of the files
        foreach($contents as $filename)
        {
            // Keep appending the file's contents
            $file .= file_get_contents($directory . $filename);
        }

        $filename = md5($file);

        // Make sure that the folder exists
        if(!file_exists($directory))
        {
            // It doesn't, so make it (allow us to write)
            mkdir($directory, 0777, true);
        }

        // Write the file to disk
        file_put_contents($directory . $filename, $file);

        // Return the file name
        return $filename;
    }
}