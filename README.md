# laravel的模型缓存

## 简介
针对laravel的模型开发的基于redis的模型数据缓存扩展

## 使用方法
* 使用继承基类的方式
```php
<?php
namespace App\Models;

use Baoziyo\ModelCache\Model;

class BaseModels extends Model
{
    //
}
```

* 使用单个model继承
```php
<?php
namespace App\Models;

use Baoziyo\ModelCache\Model;

class User extends Model
{
    //
}
```

* 禁用模型缓存
```php
namespace App\Models;

use Baoziyo\ModelCache\Model;

class User extends Model
{
    protected $disableCache = true;
    //
}
```

* 禁用模型方法缓存
```php
namespace App\Models;

use Baoziyo\ModelCache\Model;

class User extends Model
{
    protected $disableCacheFunctions = [
        // function name
    ];
    //
}
```

## 安装
```shell script
composer require baoziyo/laravel-model-cache
```

## 发布配置文件
```shell script
php artisan vendor:publish --tag=config
```

## 清空redis模型缓存
```shell script
php artisan modelCache:clear
```

## 助手方法
```
1.清空缓存
app('modelCache')->clearCache();
```

