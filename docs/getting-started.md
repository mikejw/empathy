

Getting started with Empathy
===

Requirements
---
* PHP version 8.1 is recommended  


Getting Started
---


##### Composer setup

Create a new project folder and inside a new composer configuration file:

<pre><code class="language-bash">mkdir -p ~/code/myproject; cd ~/code/myproject
vim composer.json
</code></pre>


Enter the minimal config to include the Empathy library.

<pre><code class="language-json">{
    "require": {
        "mikejw/empathy": "dev-master"
    },
    "minimum-stability": "dev"
}
</code></pre>


If composer is not installed, install it with instructions at `https://getcomposer.org/download/`
or use `wget https://getcomposer.org/composer.phar`.

Run composer install and then launch the empathy New App command line command:

<pre><code class="language-bash">php composer.phar install
php ./vendor/bin/empathy --new_app
</code></pre>

##### Directory path


The first thing you will be prompted for is the full file path to the app directory. Use the autofilled 
path unless you are using the [base-docker](/docs/base-docker/) Docker development environment for Empathy, where
the path should be set to `/var/www/project`.

If you chose the wrong path for document root ("doc_root"), you can always edit it later in `/config.yml`.


##### Choose a name


Choose a simple name for your app or choose default.

##### Set a web root


Choose the web root where your app will be accessible using your browser in development. 
E.g. `http://localhost:8080`.

If you are using "base-docker", use `www.dev.org` (and ensure this domain is aliased to 127.0.0.1 in 
your `/etc/hosts` file).


##### Disable library testing mode

Ensure the testing mode setting for the `ELib` plugin has been disabled:

<pre><code class="language-yaml">plugins:
  -
    name: ELibs
    version: 1.0
    config: '{ "testing": 0 }'
</code></pre>


##### Build frontend assets


Build frontend assets for the application by running from the root directory:

<pre><code class="language-bash">npm install
make grunt
</code></pre>

These assets are used for internal routes served by Empathy. E.g.  "/empathy/status" and "/empathy".
They are also for backend/admin area modules when using [elib-base](/docs/elib-base/). 


##### Launch

Launch PHP's inbuilt development web server (when a web root of `localhost:8080` has been
chosen).

<pre><code class="language-bash">cd ./public_html
php -S 0.0.0.0:8080
</code></pre>



##### Development settings


To aid development it is recommended to use the following configuration in `/config.yml`:

<pre><code class="language-yaml">boot_options:
  handle_errors: true
  environment: dev
  debug_mode: true
</code></pre>


This means all relevant errors/exceptions will be caught by empathy and an error page displayed to you
giving you the error details. If this isn't helping reveal the issue, you can always set 
`handle_errors` to false to use default PHP error handling.


Next
---
Go to [Understanding Routing](./routing.md).
