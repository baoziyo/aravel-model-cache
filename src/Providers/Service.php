<?php

namespace Baoziyo\ModelCache\Providers;

use Baoziyo\ModelCache\Console\Commands\Clear;
use Baoziyo\ModelCache\Helper;
use Baoziyo\ModelCache\RedisCache;
use Illuminate\Contracts\Foundation\CachesConfiguration;
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

        if (!($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app['config']->get('logging', []);
            $config['channels'] += require __DIR__.'/../../config/logging.php';
            $this->app['config']->set('logging', $config);
        }
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
        return $this->app->make('redis')::connection('modelCache');
    }
}
