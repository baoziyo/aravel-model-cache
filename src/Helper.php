<?php

namespace Baoziyo\ModelCache;

use Illuminate\Container\Container;

class Helper
{
    public function clearCache()
    {
        return $this->getRedis()->flushAll();
    }

    private function getRedis(): RedisCache
    {
        return Container::getInstance()->make(RedisCache::class);
    }
}
