<?php

namespace Baoziyo\ModelCache\Console\Commands;

use Baoziyo\ModelCache\RedisCache;
use Illuminate\Console\Command;
use Illuminate\Container\Container;

class Clear extends Command
{
    protected $signature = 'modelCache:clear';
    protected $description = '清空 model-cache 缓存';

    public function handle()
    {
        if ($this->getRedis()->flushAll()) {
            $this->info('清空成功.');

            return;
        }

        $this->error('清空失败.');

        return;
    }

    private function getRedis(): RedisCache
    {
        return Container::getInstance()->make(RedisCache::class);
    }
}
