<?php

namespace Awjudd\AssetProcessor;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'asset-processor';
    }
}