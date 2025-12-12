 

Empathy PHP Framework
===

<p>&nbsp;</p>
<p align="center">
   <img width="210" height="209" src="https://raw.githubusercontent.com/mikejw/empathy/master/eaa/public_html/img/empathy.png" alt="Empathy logo" />
</p>
<p align="center"><a href="https://empathy.sh" target="_blank">empathy.sh</a></p>

Welcome to the Empathy (micro MVC) PHP Framework project.
The project has existed since 2008 and is geared towards allowing anyone
to create PHP web applications, with following properties:

- Strict MVC pattern
- Modular compatible libraries
- Lightweight
- Fast
- Extensible

Documentation
---
See [getting-started.md](./docs/getting-started.md).


Licence
---
Empathy and officially released extension libraries are now distributed under an
MIT license.  See [LICENSE](./LICENSE).


Versioning & stability policy
---

Empathy follows semantic versioning. Early releases (v1.x → v4.x) reflect a fast-evolving design phase of the framework. Particularly
the interplay beteen models and the ORM as it was security hardened. Plus the changes required for PHP 8.4 support.  As the API matured, each breaking change was given its own major release.

With this history, major version bumps should not be seen as instability or sloppy development — but as honest signposts of sound, considered evolution.

Going forward (from v5.0.0 onward):

* Breaking changes will be batched into less-frequent major releases.

* Where possible, functionality will first be deprecated in a release, and only removed in the next major.

* Minor and patch versions will avoid breaking existing public APIs.

* Each major release will include a clear upgrade guide (e.g. a file like UPGRADING-5.0.md) with before/after code examples for users.

* Users who prefer stability over latest features can safely pin to a major version (e.g. "vendor/empathy": "^4.0") and skip new majors until explicitly ready.


Testing the code itself
---

If you want to run tests from a version of Empathy that has been checked out in [base-docker](/docs/base-docker/)
connect to the app container first, change to the empathy vendor directory and run composer install:

<pre><code class="language-bash">docker exec -it -u www-data app /bin/bash
cd ./vendor/mikejw/empathy/
php ../../../composer.phar install --prefer-source
</code></pre>

Within the 'Empathy Architype Application' config file (`/eaa/config.yml`), set: `doc_root` to
the full location of the `eaa` directory, (which is used for dummy configuration) e.g:

<pre><code class="language-yaml">---
doc_root: /var/www/project/vendor/mikejw/empathy/eaa
</code></pre>

For the `elibs` plugin configuration (within `/eaa/config.yml`), set testing mode to 1. (This makes sure
that the elibs repo containing Smarty can be found.) i.e:

<pre><code class="language-yaml">plugins:
  -
    name: ELibs
    version: 1.0
    config: '{ "testing": 1 }'
</code></pre>

Change to the `t` directory from the root of the empathy repo and run phpunit:

<pre><code class="language-bash">cd /var/www/project/vendor/mikejw/empathy/t
php ../vendor/bin/phpunit .
</code></pre>



