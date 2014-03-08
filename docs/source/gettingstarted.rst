
Getting Started
===============


Creating Your First Empathy Application
---------------------------------------

The first thing to decide is if you are going to use an associated virtual host
for your first application.  This decision will determine what to set the three
most important configuration options outlined below. It is recomended that you
don't use a new virtual host if you are going to be developing on your local
machine or workstation.  The main advantage is that if you are working on
multiple projects at once you will not need to remember lots of URLs and
locations of websever configuration files.

Typically with Apache your machine will be pre-configured with a default
virtual host that is set up to serve files under :file:`/var/www/` and can be
accessed using the URL 'http://localhost/'. Using this default means that this
is where you should place your application and then test it using the URL::

    http://localhost/myapp/

However the real URL will be::

    http://localhost/myapp/public_html/

This is due to the default Empathy directory structure reserving a
:term:`public direcory` where files are served from, for the sake of security
and better organisation.  When it comes to deploying the application you can
easily configure your webserver to serve from the public directory itself. As
an example consider the URL for my blog::

    http://www.mikejw.com/

This is an Empathy application that has been set up with Apache to have files
served from :file:`/var/www/mikejw/public_html/` on the server.  So in its live
environment the configuration is::

    doc_root: /var/www/mikejw
    web_root: www.mikejw.com
    public_dir:                                     # empty

When working on this site locally the configuration is the following::

    doc_root: /var/www/mikejw
    web_root: localhost/mikejw
    public_dir: /public_html

The reason this is important is so that [routing] works correctly. So
assuming the recommended configuration with Apache, create your first
application create a new empty directory inside :file:`/var/www` (or wherever
you have decided to have it served from) and then paste the following into a
new file called :file:`composer.json`.::

    {
        "name": "My first Empathy app.",
        "description": "An empathy web app with modern PHP-FIG goodness.",
       
        "require": {
            "mikejw/empathy": "dev-master"
        }
    }

Next install Empathy into the directory by running::

    $ composer install

You should now have a directory under :file:`/var/www/` e.g.
:file:`/var/www/firstapp` and inside :file:`firstapp` the file
:file:`composer.json` with the above contents. Once the composer install
command has been run you will also have a :file:`composer.lock` file and a new
directory called :file:`vendor`. (For more information on Composer see
http://getcomposer.org/.)

Configuring
-----------
Next up is creating the folder structure and configuring.

To begin type::

    $ ./vendor/bin/empathy --new_app

and answer the questions. This will create the minimal file/folder structure
for working with Empathy.

Next put the following into the file called :file:`config.yml`:: 

    ---
    doc_root: /opt/local/apache2/htdocs/firstapp    # path to the app
    web_root: localhost/firstapp                    # url for accessing the app
    public_dir: /public_html                        # where files are served

    plugins:
      - name: Smarty
        version: 1.0
        class_path: Smarty/Smarty.class.php
        class_name: \Smarty
        loader:


The Smarty plugin is essential at this stage because it is responsibe for
rendering the view - think HTML templates. (As of writing there is only one
other plugin available for serving the view of an application but it doesn't
deal with HTML.)

You are now ready to try out the app and see if it works so navigate to::

    http://localhost/firstapp/public_html/

If the app is set up correctly at this point you will see the following error::

    Fatal error: Smarty error: the $compile_dir '...' does not exist,
    or is not a directory...

To fix this we need to create the template cache directory for Smarty and we do
this with this shortcut command::

    ./vendor/bin/empathy --misc tpl_cache

(This creates the directory path and chmods the cache directory to fully
writable.)

If you run this command and try navigating to the above URL you should now see
a 'Success!' message.

Finally, Empathy relies on Apache :term:`mod_rewrite` for routing URLs so the
recommened aproach is to first of all ensure that the :term:`AllowOverride`
directive has been set to 'All' for your current virtual host.  See 
http://httpd.apache.org/docs/2.2/mod/core.html#allowoverride for more info.
Once this is done we can create our :file:`.htaccess` file that will enable
:term:`mod_rewite` and set up the simple rules.  The :file:`.htaccess` file
needs to live in the :term:`public_dir` directory, typically called
:file:`public_html`, alongside the :file:`index.php` file.

The contents of this file will be::

    RewriteEngine on
    RewriteBase /firstapp/public_html/

    RewriteRule ^([_a-z0-9-]/*)*$ index.php

To test this is working we need to add the following configuration block to the
:file:`config.yml` file::

    boot_options:
      handle_errors: true 
      debug_mode: true

The complete config file should now look like this::

    ---
    doc_root: /opt/local/apache2/htdocs/first       # path to the app
    web_root: moonchild/first                       # url for accessing the app
    public_dir: /public_html                        # where files are served

    plugins:
      - name: Smarty
        version: 1.0
        class_path: Smarty/Smarty.class.php
        class_name: \Smarty
        loader:
    
    boot_options:
      handle_errors: true
      debug_mode: true

Now navigate to the following URL::

    http://localhost/firstapp/public_html/foo

and you should see the following error::

    Dispatch error 1 : Missing class file

This means Empathy was unable to map the URL to anything inside the
application.  This is what we expect. Before learning about routing it is
recommended that you read the next section which is about the MVC itself.




