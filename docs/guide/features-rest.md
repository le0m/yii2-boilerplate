REST application
================

The template comes with an application ready for serving a REST API.

Separate `config` files are available for log targets and URL manager, since these can grow big with time.

User **autologin**, **session** and **CSRF** are disabled. **Request** and **response** are configured to talk UTF-8 JSON.

The API let you serve different versions at the same time. By default an empty first version is configured as example.

The folder structure is like this:

```
|- api
|
|--- versions
|
|----- v1
|------- controllers
|------- models
|------- RestModule.php
|
|----- v2
|------- ...

```

And the `Module` is configured in `main.php` like this:

```php
'modules' => [
    'v1' => [
        'class' => 'api\versions\v1\RestModule'
    ]
]
```