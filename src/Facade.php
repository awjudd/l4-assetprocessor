<?php

namespace Awjudd\AssetProcessor;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'asset-processor';
    }
}
