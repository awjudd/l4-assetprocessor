<?php namespace Awjudd\AssetProcessor\Commands;

use Awjudd\AssetProcessor\AssetProcessor;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ProcessCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'assetprocessor:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Force the processing of specific files so that they don't need to be processed at run time.";

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        $app = app();

        return array(
            array('file', '-f', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'An array of all files to process'),
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function fire()
    {
        // Grab the files
        $files = $this->option('file');

        // Check if any files existed
        if(count($files)==0)
        {
            throw new \Exception(\Lang::get('assetprocessor::errors.command.process.asset-files-required'));
        }

        // Force the processing of all files
        \Config::set('assetprocessor::enabled.force', true);

        // Cycle through all of the files
        foreach($files as $file)
        {
            // Add in the assets provided
            \AssetProcessor::add($file, $file);
        }

        // Generate the single file
        \AssetProcessor::generateSingularFile('js');
        \AssetProcessor::generateSingularFile('css');
    }
}