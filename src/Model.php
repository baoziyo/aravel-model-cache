<?php

namespace Baoziyo\ModelCache;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    protected $disableCache = false;
    protected $disableCacheFunctions = [];

    public function primaryKey()
    {
        return $this->primaryKey;
    }

    public function table()
    {
        return $this->table;
    }

    protected function newBaseQueryBuilder()
    {
        if ($this->disableCache || !$this->getRedis()->canUse()) {
            return $this->getConnection()->query();
        }

        if (!empty(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6)[5]['function']) && in_array(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6)[5]['function'], $this->disableCacheFunctions)) {
            return $this->getConnection()->query();
        }

        $conn = $this->getConnection();
        $grammar = $conn->getQueryGrammar();

        $queryBuilder = new QueryBuilder($conn, $grammar, $conn->getPostProcessor());
        $queryBuilder->setTable($this->table);
        $queryBuilder->setModel($this);

        return $queryBuilder;
    }

    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    private function getRedis(): RedisCache
    {
        return Container::getInstance()->make(RedisCache::class);
    }
}
