

Getting Started
===

Composer setup
---

Create a new project folder and inside a new composer configuration file:

    mkdir project; cd project
    vim composer.json

Enter the minimal config to include the Empathy library.
    {
        "require": {
            "mikejw/empathy": "dev-master"
        },
        "minimum-stability": "dev"
    }

If composer is not installed, install it with instructions at `https://getcomposer.org/download/`
or use `wget https://getcomposer.org/composer.phar`.

Run composer install and then launch the empathy New App command line command:

    php composer.phar install
    php ./vendor/bin/empathy --new_app

Directory path
---

The first thing the `--new_app` command will ask you is the full file path to the app. Use the autofilled 
path unless you are using the "base-docker" Docker development environment for Empathy.

If you chose the wrong path for document root ("doc_root"), you can always edit it later in `/config.yml`.

NB: If you are using "base-docker" use the doc root of "/var/www/project".

Choose a name
---

Choose a simple name for your app.

Set a web root
---

Choose the web root where your app will be accessible using your browser in development. 
E.g. `http://localhost:3000`.

If you are using "base-docker", use `www.dev.org` (and ensure this domain is aliased to 127.0.0.1 in 
your `/etc/hosts` file).

Disable library testing mode
---

Ensure the testing mode setting for the `ELib` plugin has been disabled:

    plugins:
      -
        name: ELibs
        version: 1.0
        config: '{ "testing": 0 }'

Launch
---

Launch PHP's inbuilt development web server (when a web root of `http://localhost:8080` has been
chosen).

    cd ./public_html
    php -S 0.0.0.0:8080

Build frontend assets
---

Build frontend assets for the application by running from the root directory:

    npm install
    make grunt

These assets are used for all internal routes served by Empathy and for backend/admin area
modules when using `elib-base`. E.g.  "/empathy/empathy/status" and "/empathy".

Development settings
---

To aid development it is recommended to use the following configuration in `/config.yml`:

    boot_options:
      handle_errors: true
      environment: dev
      debug_mode: true

This means all errors/exceptions will be caught by empathy and an error page displayed to you
giving you the error details. If this isn't helping reveal the issue, you can always set 
`handle_errors` to false to use default PHP error handling.


Next
---
Go to [Understanding Routing](./routing.md).