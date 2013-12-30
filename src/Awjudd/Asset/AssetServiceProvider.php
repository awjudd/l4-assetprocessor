<?php namespace Awjudd\Asset;

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
		// Bind to the "Asset" section
		$this->app->bind('asset', function() {
			return new Asset();
		});
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

}