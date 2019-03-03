Yii 2 Boilerplate Template
==================================

This Yii2 boilerplate template is based on the [yii2-app-practical-a template](https://github.com/kartik-v/yii2-app-practical-a/).

The template includes four tiers: front end, back end, console and REST, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Detailed documentation can be referred at [docs/guide/README.md](docs/guide/README.md).

DIRECTORY STRUCTURE
-------------------

```
/
    /                    contains the frontend entry script, favicon, and robots.txt.
    assets/              contains frontend application runtime web assets such as JavaScript and CSS
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    /                    contains the backend entry script, favicon, and robots.txt.
    assets/              contains the backend application runtime web assets such as JavaScript and CSS
    assets_b/            contains web assets and scripts used by backend application
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
frontend
    assets/              contains web assets and scripts used by frontend application
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
