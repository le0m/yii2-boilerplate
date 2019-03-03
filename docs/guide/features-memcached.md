Why memcached?
==============

From my researches (not much actual experience), `memcached` is trivial to setup while having good performances.

* `memcache` does not support multi-get/set, CAS tokens (i don't know if Yii2 makes use of those) and is discontinued (?)
* `redis` has overall better performance, has better memory usage (`memcached` does not return memory to the system when flushed) but is more complex to setup

Install memcached
=================

### Ubuntu 18.04 ###

1. install `memcached` and prerequisite:

    ```bash
    $ sudo apt install libmemcached11 memcached
    ```
1. install php module:

    ```bash
    $ sudo apt install php-memcached
    ```

1. (**optional**) adjust settings:

    ```bash
    $ sudo nano /etc/memcached.conf
    ```

### App config ###

Config file for cache component is `common/config/main.php`, by default uses file cache. Uncomment and adjust `MemCache` configuration.
