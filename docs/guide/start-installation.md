Installation
============

## Requirements

The minimum requirement by this project template is that your Web server supports PHP 7.0.0. Fuck PHP < 7. It's best for you, It's best for everyone.

## Installing using Composer

If you do not have [Composer](http://getcomposer.org/), follow the instructions in the
[Installing Yii](https://github.com/yiisoft/yii2/blob/master/docs/guide/start-installation.md#installing-via-composer) section of the definitive guide to install it.

With Composer installed, you can then install the application using the following commands:

    composer create-project --prefer-dist le0m/yii2-boilerplate app

The command installs the application in a directory named `app`. You can choose a different
directory name if you want.

It uses [asset-packagist](https://asset-packagist.org/) for managing bower and npm package dependencies through Composer.

## Preparing application

After you install the application, you have to conduct the following steps to initialize
the installed application. You only need to do these once for all.

1. Create a new database and adjust the `components['db']` configuration in `common/config/main-local.php` for all your `environments/` accordingly

1. Open a console terminal, execute the `init` command and select `dev` as environment.

   ```
   php /path/to/yii-application/init
   ```

   If you automate it with a script you can execute `init` in non-interactive mode.

   ```
   php /path/to/yii-application/init --env=Development --overwrite=All
   ```

1. **if you will use provided `DbTarget` implementation**: [read here](features-log.md) how to setup

1. Open a console terminal, apply migrations with command `php /path/to/yii-application/yii migrate`.

1. Set document roots of your web server:

   - for frontend `/path/to/yii-application/` and using the URL `http://domain/`
   - for backend `/path/to/yii-application/backend/` and using the URL `http://backend.domain/`
   - for backend `/path/to/yii-application/rest/` and using the URL `http://rest.domain/`

   For Apache it could be the following:

   ```apache
       <VirtualHost *:80>
           ServerName domain
           DocumentRoot "/path/to/yii-application/"
           
           <Directory "/path/to/yii-application/">
               # use mod_rewrite for pretty URL support
               RewriteEngine on
               # If a directory or a file exists, use the request directly
               RewriteCond %{REQUEST_FILENAME} !-f
               RewriteCond %{REQUEST_FILENAME} !-d
               # Otherwise forward the request to index.php
               RewriteRule . index.php

               # use index.php as index file
               DirectoryIndex index.php

               # ...other settings...
               # Apache 2.4
               Require all granted
               
               ## Apache 2.2
               # Order allow,deny
               # Allow from all
           </Directory>
       </VirtualHost>
       
       <VirtualHost *:80>
           ServerName backend.domain
           DocumentRoot "/path/to/yii-application/backend/"
           
           <Directory "/path/to/yii-application/backend/">
               # use mod_rewrite for pretty URL support
               RewriteEngine on
               # If a directory or a file exists, use the request directly
               RewriteCond %{REQUEST_FILENAME} !-f
               RewriteCond %{REQUEST_FILENAME} !-d
               # Otherwise forward the request to index.php
               RewriteRule . index.php

               # use index.php as index file
               DirectoryIndex index.php

               # ...other settings...
               # Apache 2.4
               Require all granted
               
               ## Apache 2.2
               # Order allow,deny
               # Allow from all
           </Directory>
       </VirtualHost>
       
       <VirtualHost *:80>
           ServerName rest.domain
           DocumentRoot "/path/to/yii-application/rest/"
           
           <Directory "/path/to/yii-application/rest/">
               # use mod_rewrite for pretty URL support
               RewriteEngine on
               # If a directory or a file exists, use the request directly
               RewriteCond %{REQUEST_FILENAME} !-f
               RewriteCond %{REQUEST_FILENAME} !-d
               # Otherwise forward the request to index.php
               RewriteRule . index.php

               # use index.php as index file
               DirectoryIndex index.php

               # ...other settings...
               # Apache 2.4
               Require all granted
               
               ## Apache 2.2
               # Order allow,deny
               # Allow from all
           </Directory>
       </VirtualHost>
   ```

   For nginx:

   ```nginx
       server {
           charset utf-8;
           client_max_body_size 128M;

           listen 80; ## listen for ipv4
           #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

           server_name domain;
           root        /path/to/yii-application/;
           index       index.php;

           access_log  /path/to/yii-application/log/frontend-access.log;
           error_log   /path/to/yii-application/log/frontend-error.log;

           location / {
               # Redirect everything that isn't a real file to index.php
               try_files $uri $uri/ /index.php$is_args$args;
           }

           # uncomment to avoid processing of calls to non-existing static files by Yii
           #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
           #    try_files $uri =404;
           #}
           #error_page 404 /404.html;

           # deny accessing php files for the /assets directory
           location ~ ^/assets/.*\.php$ {
               deny all;
           }

           location ~ \.php$ {
               include fastcgi_params;
               fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
               fastcgi_pass 127.0.0.1:9000;
               #fastcgi_pass unix:/var/run/php5-fpm.sock;
               try_files $uri =404;
           }
       
           location ~* /\. {
               deny all;
           }
       }
        
       server {
           charset utf-8;
           client_max_body_size 128M;
       
           listen 80; ## listen for ipv4
           #listen [::]:80 default_server ipv6only=on; ## listen for ipv6
       
           server_name backend.domain;
           root        /path/to/yii-application/backend/;
           index       index.php;
       
           access_log  /path/to/yii-application/log/backend-access.log;
           error_log   /path/to/yii-application/log/backend-error.log;
       
           location / {
               # Redirect everything that isn't a real file to index.php
               try_files $uri $uri/ /index.php$is_args$args;
           }
       
           # uncomment to avoid processing of calls to non-existing static files by Yii
           #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
           #    try_files $uri =404;
           #}
           #error_page 404 /404.html;

           # deny accessing php files for the /assets directory
           location ~ ^/assets/.*\.php$ {
               deny all;
           }

           location ~ \.php$ {
               include fastcgi_params;
               fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
               fastcgi_pass 127.0.0.1:9000;
               #fastcgi_pass unix:/var/run/php5-fpm.sock;
               try_files $uri =404;
           }
       
           location ~* /\. {
               deny all;
           }
       }
        
       server {
           charset utf-8;
           client_max_body_size 128M;
       
           listen 80; ## listen for ipv4
           #listen [::]:80 default_server ipv6only=on; ## listen for ipv6
       
           server_name rest.domain;
           root        /path/to/yii-application/rest/;
           index       index.php;
       
           access_log  /path/to/yii-application/log/rest-access.log;
           error_log   /path/to/yii-application/log/rest-error.log;
       
           location / {
               # Redirect everything that isn't a real file to index.php
               try_files $uri $uri/ /index.php$is_args$args;
           }
       
           # uncomment to avoid processing of calls to non-existing static files by Yii
           #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
           #    try_files $uri =404;
           #}
           #error_page 404 /404.html;

           # deny accessing php files for the /assets directory
           location ~ ^/assets/.*\.php$ {
               deny all;
           }

           location ~ \.php$ {
               include fastcgi_params;
               fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
               fastcgi_pass 127.0.0.1:9000;
               #fastcgi_pass unix:/var/run/php5-fpm.sock;
               try_files $uri =404;
           }
       
           location ~* /\. {
               deny all;
           }
       }
   ```

1. Change the hosts file to point the domain to your server.

   - Windows: `c:\Windows\System32\Drivers\etc\hosts`
   - Linux: `/etc/hosts`

   Add the following lines:

   ```
   127.0.0.1   domain
   127.0.0.1   backend.domain
   127.0.0.1   rest.domain
   ```

To login into the application, you need to first sign up, with any of your email address, username and password. You will receive an email to activate your account. Click the link,
then you can login into the application with same email address and password at any time.


> Note: if you want to run advanced template on a single domain so `/` is frontend and `/admin` is backend, refer
> to [Using advanced project template at shared hosting](topic-shared-hosting.md).
