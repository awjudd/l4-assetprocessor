<?php

namespace Awjudd\AssetProcessor;

use Illuminate\Support\ServiceProvider;
use Awjudd\AssetProcessor\AssetProcessor;

class AssetProcessorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->handleConfigs();
        $this->setupProcessor();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('asset-processor', AssetProcessor::class);
    }

    /**
     * Register the configuration.
     */
    private function handleConfigs()
    {
        $configPath = __DIR__.'/../config/asset-processor.php';
        $this->publishes([$configPath => config_path('asset-processor.php')]);
        $this->mergeConfigFrom($configPath, 'asset-processor');
    }

    private function setupProcessor()
    {
        $this->app->singleton(AssetProcessor::class);
    }
}
