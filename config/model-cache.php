<?php

return [
    'cache-prefix' => 'modelCache',
    'enabled' => env('MODEL_CACHE_ENABLED', false),
    'expiration' => 7200,
];
