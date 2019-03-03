TODO
===============================

* use DB implementation of `Session`
* behaviors injecting `ActiveRecord::load()` and `ActiveRecord::save()` to set flash messages on errors
* behaviors for `ModelSearch` to handle searching with a datetime range
* behavior for `ActiveRecord` to save multiple files related to record
* kartik `DateControl` module?
* regenerate `auth_key` after some time
* i18n backend views and "extract" button
* permissions and other default behaviors for all controllers (look into `RateLimiter` and `HttpSomething` ActionFilter; cache headers?)

Later
=====

* make `Settings` table a configuration
* `Setting`: refactor `refreshCache` to `invalidateCache()`
* `Setting`: add `$modelClass` property
* look into 'HTTP_X_GEO_COUNTRY' as used by mod_geoip in apache for `LanguageSelector` (locale? timezone?)
* enhance `LanguageSelector` to handle changing language with a query parameter (`LanguageAction`?)
* enhance log views with `Target` CRUDs and status toggling (and `Target` name as column for filtering?); `Logs` component
* enhance RBAC with complete CRUD handling

References
==========

[yii2tech/ar-file](https://github.com/yii2tech/ar-file) for virtual properties (see `yii2tech\ar\file\FileBehavior::fileAttribute`)

[codemix/yii2-localeurls](https://github.com/codemix/yii2-localeurls) for a way better and more complete implementation of `LanguageSelector`

Typos and renaming
==================

* change `Settings` component name and table to `Parameters`
* rename `User::$failed_logins` to `$failed_login`
