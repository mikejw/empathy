
Getting Started
===============

These instructions should help you get familiarised with Empathy by creating
your first Empathy web application, talking through its basic configuration
and setup and explaining some basic inbuilt functionality.


Creating Your First Empathy Application
---------------------------------------

The first thing to decide is if you are going to use an associated virtual host for your
new app.  This decision will determine what to set the three most important configuration
options and it is recomended that you don't use a new virtual host if you are going to
be developing on your local machine or workstation.  The main advantage is that 
if you are working on multiple web apps you don't need to remember lots of URLs or
separate virtual host configuration files (where there might be other config) for simply
working on websites that you are developing locally.

Typically Apache will be pre-configured with a default virtual host that is set up to
serve files under '/var/www/' and can be accessed using the URL 'http://localhost/'.
Using this default it means this is where you should place your apps and then test/debug
them using the URL::

    http://localhost/myapp/

Althought the real URL will be::

    http://localhost/myapp/public_html/

because the default Empathy directory structure reserves a public direcory where files are
served from for the sake of security and better organisation.  When it comes to deploying 
the app you can easily setup your webserver to serve from the public directory. i.e.::

    http://www.mikejw.com/
is actually serving files from '/var/www/mikejw/public_html/'' on the server.  So when working 
on this site locally the configuration is the following::

    doc_root: /var/www/mikejw
    web_root: localhost/mikejw
    public_dir: /public_html

and when live the configuration is::

    doc_root: /var/www/mikejw
    web_root: mikejw.com
    public_dir:                                     # empty


To create your first app simply create a new empty directory inside '/var/www' (or wherever
you have decided to serve the app from) and then paste in the following a new file called 'composer.json'.::

    {
        "name": "My first Empathy app.",
        "description": "An empathy web app with all the modern PHP-FIG goodness (hopefully).",
       
        "require": {
            "mikejw/empathy": "dev-master"
        }
    }

Next install Empathy into the directory by running::

    $ composer install

You should now have a directory under '/var/www/' e.g. '/var/www/firstapp' and inside 'firstapp' the file 
'composer.json' with the above contents. Once the composer install command has been run you will also have a 'composer.lock' 
file and a new directory called 'vendor'. (For more information on Composer see http://getcomposer.org/.)

Configuring
-----------
Next up is creating the folder structure and configuring the app.

To begin type::

    $ ./vendor/bin/empathy --new_app

and answer the questions. This will create the minimal file/folder structure
for working with Empathy.

Next put the following into the file called 'config.yml' file:: 

    ---
    doc_root: /opt/local/apache2/htdocs/firstapp    # path to the app
    web_root: localhost/firstapp                    # url for accessing the app locally
    public_dir: /public_html                        # directory where files are actually served from

    plugins:
      - name: Smarty
        version: 1.0
        class_path: Smarty/Smarty.class.php
        class_name: \Smarty
        loader:


The Smarty plugin is essential at this stage because it is responsibe for rendering the view - think HTML templates. (As
of writing there is only one other plugin available for serving the view of an application but it doesn't deal
with HTML.)

You are now ready to try out the app and see if it works so navigate to::

    http://localhost/firstapp/public_html/

If the app is set up correctly at this point you will see the following error::

    Fatal error: Smarty error: the $compile_dir '...' does not exist, or is not a directory...

To fix this we need to create the template cache directory for Smarty and we do this with this shortcut command::

    ./vendor/bin/empathy --misc tpl_cache

(This creates the directory path and chmods the cache directory to fully writable.)

If you run this command and try navigating to the above URL you should now see a 'Success!' message.

Finally, Empathy relies on Apache 'mod_rewrite' for routing URLs so the recommened aproach is to first of all
ensure that the AllowOverride directive has been set to 'All' for your current virtual host.  See 
http://httpd.apache.org/docs/2.2/mod/core.html#allowoverride for more info. Once this is done we can create our '.htaccess' file
that will enable 'mod_rewite' and set up the simple rules.  The .htaccess file needs to live in the 'public_dir' folder, typically called 'public_html', alongside the 'index.php' file.

The contents of this file will be::

    RewriteEngine on
    RewriteBase /firstapp/public_html/

    RewriteRule ^([_a-z0-9-]/*)*$ index.php

To test this is working we need to add the following configuration block to the 'config.yml' file::

    boot_options:
      environment: dev
      handle_errors: true 
      debug_mode: true

The complete config file should now look like this::

    ---
    doc_root: /opt/local/apache2/htdocs/first       # path to the app
    web_root: moonchild/first                       # url for accessing the app locally
    public_dir: /public_html                        # directory where files are actually served from

    plugins:
      - name: Smarty
        version: 1.0
        class_path: Smarty/Smarty.class.php
        class_name: \Smarty
        loader:
    
    boot_options:
      handle_errors: true
      environment: dev
      debug_mode: true

Now navigate to the following URL::

    http://localhost/firstapp/public_html/foo

and you should see the following error::

    Dispatch error 1 : Missing class file

This means Empathy was unable to map the URL to anything inside the application.  This is what we expect.
Before learning about routing it is recommended that you read the next section which is about the MVC itself.




