<?php

namespace Awjudd\AssetProcessor;

use Illuminate\Contracts\Cache\Repository as Cache;

class AssetCaching
{
    /**
     * The cache repository.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Create a new class instance.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Adds any information into the cache.
     *
     * @param string $key       (description)
     * @param <type> $processor (description)
     * @param <type> $asset     (description)
     */
    public function put($filename, $processor, $asset)
    {
    }

    /**
     * Gets the cache key.
     *
     * @param string $key The unique key for a given asset.
     */
    protected function getCacheKey($key)
    {
    }
}
