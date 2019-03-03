<?php

return [
    'name' => 'Boilerplate App',
    //'timeZone' => 'Europe/Rome', // set here the server timezone; see: http://www.php.net/manual/en/timezones.php
    //'language' => 'en-US', // handled by `BaseController`; see: http://en.wikipedia.org/wiki/IETF_language_tag
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => [
        'settings',
    ],
    'components' => [
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            //'locale' => 'en-US', // this defaults to app 'language', handled by `BaseController`
            //'timeZone' => 'Europe/Rome', // this defaults to app 'timeZone', handled by `BaseController`
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            //'class' => 'yii\caching\MemCache',
            //'useMemcached' => true,
            //'persistentId' => 'boilerplate-cache',
            //'servers' => [
            //    ['host' => '127.0.0.1']
            //]
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'dirMode' => 0755,
            'fileMode' => 0644,
            'forceCopy' => YII_DEBUG,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/app.log',
                    'levels' => ['error', 'warning'],
                    'categories' => [],
                    'except' => [],
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'],

                ],
            ],
        ],
        'settings' => ['class' => 'common\components\Settings'],
    ],
];
