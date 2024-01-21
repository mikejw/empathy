 

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
MIT license.  See [LICENSE.txt](./LICENSE.txt).


Testing the code itself
---

Within the 'Empathy Architype Application' config file (`/eaa/config.yml`), set: `doc_root` to
the full location of the `eaa` directory, e.g:

    ---
    doc_root: /var/www/project/vendor/mikejw/empathy/eaa


For the `elibs` pluign configuration, set testing mode flag to true. i.e:

	plugins:
	  -
	    name: ELibs
	    version: 1.0
	    config: '{ "testing": "true" }'


###Important

Use max PHP version 7.4 and potentially Composer version 1 to successfully install all 
(dev) dependencies.

    composer self-update --1



