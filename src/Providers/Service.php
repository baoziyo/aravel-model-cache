<?php

namespace Baoziyo\ModelCache\Providers;

use Baoziyo\ModelCache\Console\Commands\Clear;
use Baoziyo\ModelCache\Helper;
use Baoziyo\ModelCache\RedisCache;
use Illuminate\Support\ServiceProvider;

class Service extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__.'/../../config/model-cache.php';
        $this->mergeConfigFrom($configPath, 'model-cache');

        $this->commands([
            Clear::class,
        ]);

        $this->publishes([
            $configPath => config_path('model-cache.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton('ModelCache\RedisCache', function () {
            return new RedisCache($this->getRedis());
        });

        $this->app->bind('modelCache', Helper::class);
    }

    private function getRedis()
    {
        $redis = $this->app->make('redis');

        return $redis::connection('modelCache');
    }
}
