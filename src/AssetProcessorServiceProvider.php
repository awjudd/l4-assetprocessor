<?php

namespace Awjudd\AssetProcessor;

use Illuminate\Support\ServiceProvider;

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
