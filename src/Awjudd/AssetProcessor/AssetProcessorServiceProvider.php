<?php namespace Awjudd\AssetProcessor;

use Awjudd\AssetProcessor\Commands\CleanupCommand;
use Awjudd\AssetProcessor\Commands\ProcessCommand;
use Illuminate\Support\ServiceProvider;

class AssetProcessorServiceProvider extends ServiceProvider
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
        $this->registerConfiguration();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Merge the configurations
         $this->mergeConfigFrom(
            $this->baseConfigurationFile(), 'assetprocessor'
        );

        // Register the application bindings
        $this->registerBindings();

        // Register the artisan commands
        $this->registerCommands();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('assetprocessor');
    }

    /**
     * Register the application bindings that are required.
     */
    private function registerBindings()
    {
        // Bind to the "Asset" section
        $this->app->bind('assetprocessor', function() {
            return new AssetProcessor();
        });
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app['command.assetprocessor.cleanup'] = $this->app->share(function($app)
        {
            return new CleanupCommand($app);
        });

        $this->commands(
            'command.assetprocessor.cleanup'
        );

        $this->app['command.assetprocessor.process'] = $this->app->share(function($app)
        {
            return new ProcessCommand($app);
        });

        $this->commands(
            'command.assetprocessor.process'
        );
    }

    /**
     * Register configuration files, with L5 fallback
     */
    protected function registerConfiguration()
    {
        $this->publishes([
            $this->baseConfigurationFile() => config_path('assetprocessor.php'),
        ]);
    }

    private function baseConfigurationFile()
    {
        return __DIR__.'/../../../config/assetprocessor.php';
    }
}
