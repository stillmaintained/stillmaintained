<?php

use Cake\Cache\Cache;
use Cake\Utility\Hash;

$cacheEngines = [
    'file' => [
        'className' => 'Cake\Cache\Engine\FileEngine',
        'serialize' => true,
        // 'mask' => 0666
    ],
    'redis' => [
        'className' => 'Cake\Cache\Engine\RedisEngine',
        'port' => '6379',
        'server' => 'localhost'
    ]
];

$cacheEngine = $cacheEngines['file'];
if (extension_loaded('redis') && class_exists('\Redis')) {
    $redis = new \Redis();
    if ($rh = $redis->connect($cacheEngines['redis']['server'], $cacheEngines['redis']['port'])) {
        $redis->close();
        $cacheEngine = $cacheEngines['redis'];
    }
}

$cacheConfig = Hash::merge([
    '_cake_core_' => $cacheEngines['file'] + ['duration' => '+999 days', 'path' => CACHE . 'persistent' . DS],
    '_cake_model_' => $cacheEngines['file'] + ['duration' => '+999 days', 'path' => CACHE . 'models' . DS],
    'default' => $cacheEngine + ['duration' => '+15 mins'],
    'year' => $cacheEngine + ['duration' => '+1 year'],
    'month' => $cacheEngine + ['duration' => '+1 month'],
    'week' => $cacheEngine + ['duration' => '+1 week'],
    'day' => $cacheEngine + ['duration' => '+1 day'],
    'asset_compress' => $cacheEngines['file'] + ['duration' => '+999 days', 'path' => CACHE . 'persistent' . DS],
], consume('Cache'));

Cache::config($cacheConfig);

unset($cacheConfig, $cacheEngine, $cacheEngines, $redis);
