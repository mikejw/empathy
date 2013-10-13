
Getting Started
===============

These instructions should help you get familiarised with Empathy by creating
your first Empathy web application. Talking through its basic configuration
and setup and explaining the basic inbuilt functionality.


Creating Your First Empathy Application
---------------------------------------

To create your first app simply create a new empty directory and then paste
in the following a new file called 'composer.json'.::

    {
        "name": "My first Empathy app.",
        "description": "An empathy web app with all the modern PHP-FIG goodness (hopefully).",
       
        "require": {
            "mikejw/empathy": "dev-master"
        }
    }

Next install Empathy into the directory by running::

    composer install


Configuring
-----------
Next up is creating the folder structure and configuring the app.

To begin type::

    ./vendor/bin/empathy --new_app

and answer the questions. This will create the minimal file/folder structure
for working with Empathy.

Next put the following into your config.yml file:: 

	---
	doc_root: /opt/local/apache2/htdocs/first
	web_root: moonchild/first
	public_dir: /public_html

	plugins:
  	  - name: Smarty
        version: 1.0
        class_path: Smarty/Smarty.class.php
        class_name: \Smarty
        loader:





