<?php namespace Awjudd\Asset\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\View\Environment
 */
class AssetFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'asset'; }

}