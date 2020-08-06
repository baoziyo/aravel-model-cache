<?php

namespace Baoziyo\ModelCache;

use Illuminate\Support\Facades\Redis;

class RedisCache
{
    /**
     * @var Redis
     */
    protected $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function get($key)
    {
        if (!$key) {
            return [];
        }

        return unserialize($this->redis::get($key));
    }

    public function del($key, $queryBuilderObj)
    {
        if (!config('model-cache.enabled', false)) {
            return $queryBuilderObj();
        }

        $this->redis::del($key);

        return $queryBuilderObj();
    }

    public function set($key, $queryBuilderObj)
    {
        if (!$key || !config('model-cache.enabled', false)) {
            return $queryBuilderObj();
        }

        if ($this->redis::exists($key)) {
            return unserialize($this->redis::get($key));
        }

        $result = $queryBuilderObj();

        if (empty($result)) {
            return $result;
        }

        $this->redis::setex($key, config('model-cache.expiration', 7200), serialize($result));

        return $result;
    }

    public function flushAll()
    {
        return $this->redis::del('*'.config('model-cache.cache-prefix', 'modelCache').':*');
    }
}
