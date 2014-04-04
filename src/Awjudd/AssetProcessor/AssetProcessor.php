<?php namespace Awjudd\AssetProcessor;

use App;
use Config;
use DirectoryIterator;
use Exception;
use HTML;
use URL;
use Lang;
use SplFileInfo;
use Str;

class AssetProcessor
{
    /**
     * The processors that will be used for the rest of the work.
     * 
     * @var array
     */
    protected $processors = array();

    /**
     * An array that will map all of the associated extensions to a specific
     * processor.
     * 
     * @var array
     */
    private $extensionMapping = array();

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
    private $files = array();

    /**
     * Which asset types have had the CDN retrieved for it?
     * 
     * @var array
     */
    private $cdnRetrieved = array();

    /**
     * Returns the base storage folder for any files.
     * 
     * @return string
     */
    public static function storageFolder()
    {
        return Config::get('assetprocessor::cache.directory') . '/';
    }


    public function __construct()
    {
        // Determine if we should process the files
        $this->deriveProcessingEnabled();

        // Set up all of the libraries that we need
        $this->setupLibraries();

        // Set up the required directories
        $this->setupDirectories();

        // Auto-load any of the assets that were specified
        $this->autoLoadAssets();
    }

    /**
     * Used in order to allow for the use of a CDN for assets.
     * 
     * @param string $name The name of the asset file
     * @param string $url The URL that we'll be retrieving the assets from
     * @param string $type Whether the file is a JavaScript file or a CSS file (if not provided 
     *          it will derive off of the name)
     * @return void
     */
    public function cdn($name, $url, $type = null)
    {
        // Check if there is a type provided
        if($type === null)
        {
            // No type provided, so derive it
            $type = substr(strrchr($url, '.'), 1);
        }

        // Grab the CDN group
        $group = Config::get('assetprocessor::attributes.group.cdn', 'cdn');

        // Add the asset type
        $this->files[$type][$group][$name] = $url;
    }

    /**
     * Used in order to add a file to the asset management system.
     * 
     * @param string $name
     * @param string $filename
     * @param array $attributes
     * @return void
     * @throws Exception
     */
    public function add($name, $filename, array $attributes = array())
    {
        // Check if the file exists
        if(!file_exists($filename))
        {
            // The file doesn't exist, so throw an exception
            throw new Exception(Lang::get('assetprocessor::errors.asset.file-not-found', array('file' => $filename)));
        }

        // Figure out which asset group we are in
        $group = isset($attributes['group']) ? $attributes['group'] : Config::get('assetprocessor::attributes.group.default');

        // Grab the file information
        $file = new SplFileInfo($filename);

        // Check if the file is a directory
        if($file->isDir())
        {
            // It was a directory, so iterate through it
            $directory = new DirectoryIterator($filename);

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

                // Whether or not we at least generate a file
                $generateFile = true;

                // Check if the file is in webroot
                if(!Str::contains($file_to_process, public_path()))
                {
                    // It isn't, so force the processing
                    $generateFile = true;
                }

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
                        throw new Exception(Lang::get('assetprocessor::errors.asset.different-asset-types', array('file' => $file_to_process)));
                    }
                    // Was there a duplicate name, and we are erroring
                    else if(isset($this->files[$assetType][$name]) && Config::get('assetprocessor::file.error-on-duplicate-name', false))
                    {
                        // We are erroring because of the duplicate name, so throw an exception
                        throw new Exception(Lang::get('assetprocessor::errors.asset.duplicate-name', array('name' => $name)));
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
                if($file_to_process != $file->getRealPath())
                {
                    // Build the metadata file name
                    $metadata = dirname($file_to_process) . '/metadata';

                    // Does the file exist?
                    if(!file_exists($metadata) || (file_exists($metadata) && filemtime($metadata) < filemtime($file_to_process)))
                    {
                        // Derive the new file name
                        $output = md5(file_get_contents($file_to_process));

                        // Write out the metadata file for next time
                        file_put_contents($metadata, $output);

                        // It was, so add it to the base folder
                        $dest_path = Config::get('assetprocessor::cache.directory') . '/' . $assetType . '/' . $output;

                        // Copy the file over
                        copy($file_to_process, $dest_path);

                        // Grab the file name to add to our asset list
                        $file_to_process = basename($dest_path);
                    }
                    else
                    {
                        // Otherwise, just read the metadata file
                        $file_to_process = file_get_contents($metadata);
                    }

                }
                // Check if we need to generate a files
                else if($file_to_process == $file->getRealPath() && $generateFile)
                {
                    // Figure out the type of the file
                    $type = $file->getExtension();

                    // We need to generate the file, so make a copy of it in the storage folder
                    $file_to_process = $this->write($type, $file_to_process);
                }

                // Add it to the list of files that we processed
                $this->files[$assetType][$group][$name] = $file_to_process;
            }

            // Does the final location for the file match the current path?
            if(Config::get('assetprocessor::cache.directory') != Config::get('assetprocessor::cache.external', Config::get('assetprocessor::cache.directory')))
            {
                // Derive the source path
                $source = Config::get('assetprocessor::cache.directory') . '/' . $assetType . '/' . basename($file_to_process);

                // Derive the destination path
                $destination = Config::get('assetprocessor::cache.external', Config::get('assetprocessor::cache.directory')) . '/' . $assetType . '/' . basename($file_to_process) . '.' . $assetType;

                // Was the final resting spot written later than the regular file?
                if(!file_exists($destination) || (filemtime($source) < filemtime($destination)))
                {
                    // Copy the file over
                    copy($source, $destination);
                }

                // Overwrite the path (only if it is a public URL)
                if(Str::contains($destination, public_path()))
                {
                    $this->files[$assetType][$group][$name] = $destination;
                }
            }
        }
    }

    /**
     * Used to retrieve a single string which will contain all styles that are
     * needed for your application.
     * 
     * @return string
     * @throws Exception
     */
    public function styles($group = NULL)
    {
        return $this->retrieve('css', $group);
    }

    /**
     * Used to retrieve a single string which will contain all JavaScript that are
     * needed for your application.
     * 
     * @return string
     * @throws Exception
     */
    public function scripts($group = NULL)
    {
        return $this->retrieve('js', $group);
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
     * Generates a singular file that contains all of the specific asset files.
     * 
     * @return string The file name
     */
    public function generateSingularFile($type, $group, $directory)
    {
        // Check if the group exists
        if(!isset($this->files[$type][$group]))
        {
            // It doesn't so we are done
            return null;
        }

        // Grab the associated assets
        $assets = $this->files[$type][$group];

        // Write out the files
        return $this->write($type, $assets, $directory);
    }

    /**
     * Used internally to retrieve a single string to used to retrieve all of the required assets.
     * 
     * @param string $type
     * @return string
     * @throws Exception
     */
    private function retrieve($type, $group)
    {
        // The string which will be emitted with all of the information
        $output = '';

        // Check if the group is provided
        if($group === NULL)
        {
            // It wasn't so default it
            $group = Config::get('assetprocessor::attributes.group.default');

            // Grab the group name for the CDN
            $cdn = Config::get('assetprocessor::attributes.group.cdn');

            // Check if the CDN group is set
            if(isset($this->files[$type][$cdn]) && !in_array($type, $this->cdnRetrieved))
            {
                // No asset group, so check if there is a CDN, and add it in
                $output .= $this->retrieve($type, $cdn);

                // Mark the CDN as retrieved
                $this->cdnRetrieved[] = $type;
            }
        }

        // Check if the group exists
        if(!isset($this->files[$type][$group]))
        {
            if(Config::get('assetprocessor::file.error-on-missing-group', true))
            {
                // It doesn't so give them an error
                throw new Exception(Lang::get('assetprocessor::errors.asset.asset-group-not-found', array(
                        'type' => $type,
                        'group' => $group,
                    )));
            }
            
            return $output;
        }

        // Are we looking at CDNs?
        if($group == Config::get('assetprocessor::attributes.group.cdn'))
        {
            // Loop through all of the files and spit out the link
            foreach($this->files[$type][$group] as $file)
            {
                // Add in a bypass since there is no point in re-processing the file
                switch($type)
                {
                    case 'js':
                        $output .= HTML::script($file);
                        break;
                    case 'css':
                        $output .= HTML::style($file);
                        break;
                }
            }

            // CDN support is done, so return
            return $output;
        }

        // The controller and method that will be used to emit the processed files
        $controller = Config::get('assetprocessor::controller.name') . '@' . Config::get('assetprocessor::controller.method');

        // Are we needing a single file?
        if(Config::get('assetprocessor::cache.singular') && $this->processingEnabled)
        {
            $assets = $this->files[$type][$group];

            // We only want a single file per type, so combine all of them together
            if(count($assets) == 1)
            {
                // There is only one, so just grab it
                $asset = current($assets);

                // Check if the asset is internal
                if(Str::contains($asset, public_path()))
                {
                    // Replace any backslashes with a regular slash (Windows support)
                    $asset = str_replace('\\', '/', str_replace(public_path(), '', $asset));

                    // It is external, so just emit it
                    // Add in the asset
                    switch($type)
                    {
                        case 'js':
                            $output .= HTML::script($asset);
                            break;
                        case 'css':
                            $output .= HTML::style($asset);
                            break;
                    }
                }
                else
                {
                    // Add in a bypass since there is no point in re-processing the file
                    switch($type)
                    {
                        case 'js':
                            $output .= HTML::script(URL::action($controller, array($type, $asset)));
                            break;
                        case 'css':
                            $output .= HTML::style(URL::action($controller, array($type, $asset)));
                            break;
                    }
                }
            }
            else
            {
                // There was more than one file, so we need to combine them all
                // into one file
                $external = Config::get('assetprocessor::cache.external', Config::get('assetprocessor::cache.directory'));
                $file = $this->generateSingularFile($type, $group, $external);

                // Check if the asset is internal
                if(Str::contains($external, public_path()))
                {
                    $asset = $external . '/' . $type . '/' . $file;

                    // Make a copy of the file with the proper extension
                    copy($asset, $asset . '.' . $type);

                    // Append the file extension
                    $asset .= '.' . $type;

                    // Replace any backslashes with a regular slash (Windows support)
                    $asset = str_replace('\\', '/', str_replace(public_path(), '', $asset));

                    // It is external, so just emit it
                    // Add in the asset
                    switch($type)
                    {
                        case 'js':
                            $output .= HTML::script($asset);
                            break;
                        case 'css':
                            $output .= HTML::style($asset);
                            break;
                    }
                }
                else
                {
                    // Add in a bypass since there is no point in re-processing the file
                    switch($type)
                    {
                        case 'js':
                            $output .= HTML::script(URL::action($controller, array($type, $file)));
                            break;
                        case 'css':
                            $output .= HTML::style(URL::action($controller, array($type, $file)));
                            break;
                    }
                }
            }
        }
        else
        {
            // We want several files for each, so return each.
            foreach($this->files[$type][$group] as $file)
            {
                // Check if the asset is internal
                if(Str::contains($file, public_path()))
                {
                    // Replace any backslashes with a regular slash (Windows support)
                    $asset = str_replace('\\', '/', str_replace(public_path(), '', $file));

                    // It is external, so just emit it
                    // Add in the asset
                    switch($type)
                    {
                        case 'js':
                            $output .= HTML::script($asset);
                            break;
                        case 'css':
                            $output .= HTML::style($asset);
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
                            $output .= HTML::script(URL::action($controller, array($type, $actual_name)));
                            break;
                        case 'css':
                            $output .= HTML::style(URL::action($controller, array($type, $actual_name)));
                            break;
                    }
                }

                
            }
        }

        return $output;
    }

    /**
     * Used internally in order to determine whether or not we need to actually
     * process the input files.
     */
    private function deriveProcessingEnabled()
    {
        // Are they forcing it to be enabled?
        if(Config::get('assetprocessor::enabled.force', false))
        {
            // It was forced, so enable it
            $this->processingEnabled = true;
        }
        else
        {
            // Otherwise derive it based on the environment that we are in
            $this->processingEnabled = in_array(App::environment()
                    , Config::get('assetprocessor::enabled.environments', array())
                );
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
        $processors = Config::get('assetprocessor::processors.types', array());

        // Grab the interface we should be implementing
        $interface = Config::get('assetprocessor::processors.interface');

        // Iterate through the list
        foreach($processors as $name => $class)
        {
            // Get an instance of the processor
            $instance = $class::getInstance($this->processingEnabled);

            // Ensure that it implements the correct interface
            if(!($instance instanceof $interface))
            {
                throw new Exception(Lang::get('assetprocessor::errors.asset.invalid-type', array('class' => $class, 'interface' => $interface)));
            }

            // Add it into the array of processors
            $this->processors[$name] = $instance;

            // Grab the list of extensions it processes
            $this->deriveExtensionMapping($name, $class::getAssociatedExtensions());

            // Check if the asset type exists
            if(!isset($this->files[$instance->getAssetType()]))
            {
                // Asset type didn't exist, so add it into the mapping
                $this->files[$instance->getAssetType()] = array();
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
                $this->extensionMapping[$extension] = array();
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
     * @param mixed $contents An array of all of the files to combine OR a single file to write
     * @param string $directory The name of the directory to store the files in
     * @return string The new file name
     */
    private function write($type, $contents, $directory = null)
    {
        // Will contain all of the files put together
        $file = '';

        // Derive the destination path
        $directory = $directory === null ? static::storageFolder() : ($directory . '/');
        $directory .= $type . '/';

        // Do we have one file or multiple?
        if(is_array($contents))
        {
            // Cycle through each of the files
            foreach($contents as $filename)
            {
                // Check if the file exists on it's own
                if(!file_exists($filename))
                {
                    $filename = $directory . $filename;    
                }
                
                // Keep appending the file's contents
                $file .= file_get_contents($filename);
            }
        }
        else
        {
            // Check if the file exists on it's own
            if(!file_exists($contents))
            {
                $contents = $directory . $contents;    
            }
            
            // Otherwise just read in the file
            $file .= file_get_contents($contents);
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

    /**
     * Used internally in order to help with the auto-loading of all of the assets.
     * 
     * @return void
     */
    private function autoLoadAssets()
    {
        // Add in the CDN-typed assets
        foreach(Config::get('assetprocessor::autoload.cdn', array()) as $asset)
        {
            // Add in the asset
            self::cdn($asset, $asset);
        }

        // Add in the local assets
        foreach(Config::get('assetprocessor::autoload.local', array()) as $asset)
        {
            // Add in the asset
            self::add($asset, $asset);
        }        
    }

    /**
     * Sets up all of the base directories that the plugin will need to run.
     * 
     * @return void
     */
    private function setupDirectories()
    {
        // Check if the internal directory is present
        if(!file_exists($dir = Config::get('assetprocessor::cache.directory')))
        {
            // It doesn't, so make the folder
            mkdir($dir, 0777, true);
        }

        // Check if the external directory is available
        if(!file_exists($dir = Config::get('assetprocessor::cache.external', Config::get('assetprocessor::cache.directory'))))
        {
            // It doesn't, so make the folder
            mkdir($dir, 0777, true);
        }

        // Check if the external directory is available (CSS)
        if(!file_exists($dir = Config::get('assetprocessor::cache.external', Config::get('assetprocessor::cache.directory')) . '/css'))
        {
            // It doesn't, so make the folder
            mkdir($dir, 0777, true);
        }

        // Check if the external directory is available (JavaScript)
        if(!file_exists($dir = Config::get('assetprocessor::cache.external', Config::get('assetprocessor::cache.directory')) . '/js'))
        {
            // It doesn't, so make the folder
            mkdir($dir, 0777, true);
        }
    }
}