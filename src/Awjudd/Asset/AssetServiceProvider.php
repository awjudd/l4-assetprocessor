<?php namespace Awjudd\Asset;

use Awjudd\Asset\Commands\CleanupCommand;
use Illuminate\Support\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('awjudd/asset');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
	   // Register the application bindings
       $this->registerBindings();

       // Register the artisan commenads
       $this->registerCommands();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('asset');
	}

    /**
     * Register the application bindings that are required.
     */
    private function registerBindings()
    {
        // Bind to the "Asset" section
        $this->app->bind('asset', function() {
            return new Asset();
        });
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app['command.asset.cleanup'] = $this->app->share(function($app)
        {
            return new CleanupCommand($app);
        });

        $this->commands(
            'command.asset.cleanup'
        );
    }

}