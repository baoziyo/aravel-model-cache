<?php

namespace Baoziyo\ModelCache;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use redis as redisService;

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

        $this->clearByKey($key);

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
        return $this->clearByKey('*'.config('model-cache.cache-prefix', 'modelCache').':*');
    }

    public function canUse()
    {
        if (!extension_loaded('redis')) {
            Log::channel('modelCache')->warning('redis扩展未安装');

            return false;
        }

        try {
            $redis = new redisService();
            $redis->connect(env('REDIS_HOST', '127.0.0.1'), env('REDIS_PORT', '6379'));
        } catch (\RedisException $e) {
            Log::channel('modelCache')->warning('redis服务未启动');

            return false;
        }

        return true;
    }

    private function clearByKey($key)
    {
        $keys = $this->redis::keys($key);
        $chunkKeys = array_chunk($keys, 1000);

        foreach ($chunkKeys as $chunkKey) {
            $this->redis::pipeline(function ($pipe) use ($chunkKey) {
                foreach ($chunkKey as $key) {
                    $pipe->del($this->replacePrefix($key));
                }
            });
        }

        return true;
    }

    private function replacePrefix($key)
    {
        return str_replace(env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'), '', $key);
    }
}
