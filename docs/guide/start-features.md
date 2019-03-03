Features
============

1. The template has some security preconfigured for users with Apache web servers. It has a default `.htaccess` security configuration setup.
2. The template has prettyUrl enabled by default and the changes have been made to `.htaccess` as well as `urlManager`
   component config in the common config directory.
3. The template has isolated cookie settings for backend and frontend so that you can seamlessly access frontend and backend from same client. 
   The config files includes special `identity` and `csrf` cookie parameter settings for backend. Edit it according to your needs if necessary.

Other
=====

* [REST api application](features-rest.md)
* [params from DB](features-settings.md)
* [language selector](features-language-selector.md)
* [log DB targets](features-log.md)
* [memcached 'cache' component](features-memcached.md)
