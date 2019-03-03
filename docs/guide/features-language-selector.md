Language Selector
=================

This component automatically select the language to use for the current user.

Accepted languages are obtained from the request header and saved to session or user model.

Config
======

Configurations for this component:

```php
'bootstrap' => [
    [
        'class' => 'common\components\LanguageSelector',
        
        'languages' => ['en'], // languages supported by the application
        
        'enableModel' => true, // enable saving preference to user model, for logged users
        'languageAttribute' => 'language', // attribute of user model to use
        
        'enableSession' => true, // enable saving preference to session, for guest users
        'sessionKey' => 'le0m/language', // key used to cache
    ],
]
```

Saving to user model only works if it's the same model implementing `IdentityInterface`, making it accessible with `Yii::$app->user->identity`.
