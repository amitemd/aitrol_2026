<?php

namespace Sis_medico\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

class V3CatalogCacheService
{
    const VERSION_KEY = 'v3_catalog_cache_version';

    public function remember($catalog, Closure $resolver, $scope = 'global')
    {
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        $minutes = max(1, (int) config('performance.catalog_cache_minutes', 10));
        $key = 'v3_catalog:'.$version.':'.$scope.':'.$catalog;

        return Cache::remember($key, $minutes, $resolver);
    }

    public static function flushCache()
    {
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        Cache::forever(self::VERSION_KEY, $version + 1);
    }
}
