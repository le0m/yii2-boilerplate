Settings in DB
==============

The `Settings` component is used to read parameters for the application from DB. A migration, model and backend views are provided to help setup, access and change the parameters.

The component reads parameters from the DB and makes them available in `Yii::$app->params`.

Config
=====

Configurations for this component:

```php
'bootstrap' => [
    'settings',
],
'components' => [
    'settings' => [
        'class' => 'common\components\Settings',
        
        'cache' => 'cache', // name of the cache component
        'cacheDuration' => 3600, // duration of the cache in seconds
        'cacheKey' => 'le0m/settings', // key used to cache
    ],
]
```
You can set `cache` to `false` to disable caching and read parameters from DB on each execution.

Methods
=======

The component exposes one function to refresh the cache after a change happened:

```php
/** @var \common\components\Settings $settings */
$settings = Yii::$app->get('settings');
$settings->refreshCache();
```

This function is used in `common\models\Setting` to refresh the cache after update, insertion or deletion of a record.
