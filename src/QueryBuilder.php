<?php

namespace Baoziyo\ModelCache;

use Illuminate\Container\Container;
use Illuminate\Database\Query\Builder;

class QueryBuilder extends Builder
{
    /**
     * @var Model
     */
    private $model;
    private $table;

    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function get($columns = ['*'])
    {
        return $this->getRedis()->set($this->getSaveCacheKey(), function () use ($columns) {
            return parent::get($columns);
        });
    }

    public function delete($id = null)
    {
        if (!config('model-cache.enabled', false)) {
            return parent::get($columns);
        }

        return $this->getRedis()->del($this->getDelCacheKey(), function () use ($id) {
            return parent::delete($id);
        });
    }

    public function update(array $values)
    {
        if (!config('model-cache.enabled', false)) {
            return parent::get($columns);
        }

        return $this->getRedis()->del($this->getDelCacheKey(), function () use ($values) {
            return parent::update($values);
        });
    }

    public function insert(array $values)
    {
        return $this->getRedis()->del($this->getDelCacheKey(), function () use ($values) {
            return parent::insert($values);
        });
    }

    public function insertGetId(array $values, $sequence = null)
    {
        return $this->getRedis()->del($this->getDelCacheKey(), function () use ($values, $sequence) {
            return parent::insertGetId($values, $sequence);
        });
    }

    private function getSaveCacheKey()
    {
        $sql = $this->buildSql();

        return config('model-cache.cache-prefix', 'modelCache').':'.$this->table.':'.$sql;
    }

    private function getDelCacheKey()
    {
        return '*'.config('model-cache.cache-prefix', 'modelCache').':'.$this->table.'*';
    }

    private function buildSql()
    {
        return vsprintf(str_replace('?', '\'%s\'', $this->toSql()), $this->getBindings());
    }

    private function getRedis(): RedisCache
    {
        return Container::getInstance()->make(RedisCache::class);
    }
}
