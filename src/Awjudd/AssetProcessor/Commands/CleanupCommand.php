<?php namespace Awjudd\AssetProcessor\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class CleanupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'assetprocessor:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes any clean up any asset files that are no longer needed.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        $app = app();

        return array(
            array('duration', '-d', InputOption::VALUE_OPTIONAL, 'The duration for files to be retained.'),
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        // Grab the duration
        $duration = $this->option('duration');

        // Check if the duration was provided
        if($duration === null)
        {
            // It was, so grab it from the configuration
            $duration = \Config::get('assetprocessor::cache.duration');
        }

        // 
    }
}