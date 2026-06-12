<?php

foreach ([
    '/tmp/finproppt/cache',
    '/tmp/finproppt/sessions',
    '/tmp/finproppt/views-price-calc-v2',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

putenv('CACHE_STORE=array');
putenv('SESSION_DRIVER=cookie');
putenv('VIEW_COMPILED_PATH=/tmp/finproppt/views-price-calc-v2');
$_ENV['CACHE_STORE'] = 'array';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/finproppt/views-price-calc-v2';

require __DIR__ . '/../public/index.php';
