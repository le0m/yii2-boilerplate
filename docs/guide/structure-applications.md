Applications
============

There are four applications in this template: frontend, backend, console and REST. Frontend is typically what is presented
to end user, the project itself. Backend is admin panel, analytics and such functionality. Console is typically used for
cron jobs and low-level server management. Also it's used during application deployment and handles migrations and assets.

REST is where you can expose your API. You can expose multiple version by creating new directories in `rest/versions/` and copying the `RestModule` there. You will also need to add the new Module to the `controllerMap` in `rest/config/main.php`

There's also a `common` directory that contains files used by more than one application. For example, `User` model.


Each application has its own namespace and alias corresponding to its name. Same applies to the common directory.
