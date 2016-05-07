<?php

namespace Awjudd\AssetProcessor;

use Illuminate\Support\ServiceProvider;

class AssetProcessorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     * 
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
    }

    /**
     * Register the configuration.
     * 
     * @return void
     */
    private function handleConfigs()
    {
        $configPath = __DIR__ . '/../config/assetprocessor.php';
        $this->publishes([$configPath => config_path('assetprocessor.php')]);
        $this->mergeConfigFrom($configPath, 'assetprocessor');
    }
}