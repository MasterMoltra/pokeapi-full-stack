<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class SimpleCache
{
    /**
     * @var string Base path of the cache directory
     */
    private const CACHE_DIR = __DIR__ . '/../../../var/cache/php';

    /**
     * @var string Default lifetime (in seconds) for cache
     */
    private const CACHE_LIFETIME = 0;

    /** @var Psr16Cache[] */
    private static $cache;

    /**
     * A private constructor; prevents direct creation of object.
     */
    private function __construct()
    {
    }

    /**
     * Get instance of Simple Cache service.
     *
     * @param string $namespace Optional cache namespace(subdirectory)
     */
    public static function getInstance(string $namespace = '')
    {
        if (empty(self::$cache[$namespace])) {
            $psr6Cache = new FilesystemAdapter(
                $namespace,
                self::CACHE_LIFETIME,
                self::CACHE_DIR
            );
            // a PSR-16 cache that uses your cache internally!
            $psr16SimpleCache = new Psr16Cache($psr6Cache);
            self::$cache[$namespace] = $psr16SimpleCache;
        }

        return self::$cache[$namespace];
    }
}
