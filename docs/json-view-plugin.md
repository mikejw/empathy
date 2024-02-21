

JSON View Plugin
===

JSONView is a core Empathy plugin that enables you to configure
one or more application modules as JSON speaking RESTFul apis.

It can be enabled on modules by adding the following to your 
`/config.yml` file:

<pre><code class="lang-yml">plugins:
  - 
    name: JSONView
    version: 1.0
    config: |
      [
        { "api": {} },
        { "api2": {} } 
      ]
</code></pre>


In this example both the `api` and `api2` modules will be 
configured to act as RESTful APIs.

This means that the plugin will be used for the view layer or 
presentation of your application at the routes (resources) you choose
within these modules.  From your controller actions
you will be able to pass data to the plugin for it to render.

There were few key concerns when developing the plugin:

* One or more modules can be configured to act as JSON APIs.
* The presentation object should always "speak JSON" even
  when dealing with errors and exceptions.
* There should be support for dealing with JSONP requests
  to ease api development.
* There are levels of error output that can be configured
  with regular `/config.yml` `boot_options` settings.
  i.e. Server Error 500s are served with no more details
  unless in 'dev' environment with `debug: true` in `/config.yml`.
* There are core objects to use when creating JSON view responses. These are:

<pre><code class="lang-vim">vendor/mikejw/empathy/src/Empathy/MVC/Plugin/JSONView/EROb.php
vendor/mikejw/empathy/src/Empathy/MVC/Plugin/JSONView/ROb.php
vendor/mikejw/empathy/src/Empathy/MVC/Plugin/JSONView/ReturnCodes.php
</code></pre>

* The response classes provide defaults out the box with simple
  api for setting response output in generic response properties.
* These classes can be replaced with custom ones (or extended from). 
  As an example here is sample
  config that uses classes that reside in the application's `/src` directory.
  (NB: for composer PSR autoloading see [here](https://getcomposer.org/doc/04-schema.md#autoload):

<pre><code class="lang-yml">plugins:
  -
    name: JSONView
    version: 1.0
    config: '{"api": {"error_ob":"Ace\\EROb","return_ob":"Ace\\ROb","return_codes":"Ace\\ReturnCodes"}}'
</code></pre>


Response Examples
---

Basic example, using the default `ROb` response
object:

<pre><code class="lang-php">&lt;?php
namespace Empathy\MVC\Controller;
use Empathy\MVC\Plugin\JSONView\ROb;

class api extends CustomController
{
    public function default_event()
    {
        $rob = new ROb();
        $data = new \stdClass();
        $data->foo = 'bar';
        $rob->setData($data);
        $this->assign('default', $rob, true);
    }
}
</code></pre>

NB: the third argument used in `assign` will enable or disable
the `no_array` option, which is off by default. When set to
true the contents of the second argument will become the main
output rendered. Otherwise, the contents will be assigned to 
the output object using the first argument as the property name.
This means you can call assign multiple times to build up the 
rendered output, when `no_array` is set to false. (The default.)
However in most cases you will want to pass a single piece of data 
and you will need to do so when passing an error in order for it
to be handled properly, like in the following example:

<pre><code class="lang-php">&lt;?php
namespace Empathy\MVC\Controller;
use Empathy\MVC\Plugin\JSONView\EROb;
use Empathy\MVC\Plugin\JSONView\ReturnCodes;

class api2 extends CustomController
{
    public function default_event()
    { 
        $response = EROb::getObject(ReturnCodes::Not_Found);
        $this->assign('default', $response, true);
    }
}
</code></pre>

To enable a JSONP response simply check for a callback value
and then call `setJSONPCallback` on your response object:

<pre><code class="lang-php">$r = new ROb();
$data = new \stdClass();
$data->foor = 'bar';
if (isset($_GET['callback'])) {
    $r->setJSONPCallback($_GET['callback']);
}
$this->assign('default', $r, true);
</code></pre>

When not returning errors, you do not need to use or extend 
your response objects and data from the included response object
classes.  You can return any variables, arrays, and vanilla objects:

<pre><code class="lang-php">$this->assign('default', [1, 2, 3, 4, 5], true);</code></pre>

Produces:

<pre><code class="lang-json">[
    1,
    2,
    3,
    4,
    5
]
</code></pre>

Or:

<pre><code class="lang-php">$this->assign('default', [1, 2, 3, 4, 5], false);</code></pre>

Produces:

<pre><code class="lang-json">{
    "default": [
        1,
        2,
        3,
        4,
        5
    ]
}
</code></pre>

Pretty Print Flags
---

To enable pretty printing in returned JSON you can
set `pretty_print` flags per api module in `/config.json`:

<pre><code class="lang-yml">plugins:
  -
    name: JSONView
    version: 1.0
    config: |
    [
      { "api": { "pretty_print": true } }
    ]
</code></pre>

Next
---
You may want to add users to your application. See [elib-base](https://github.com/mikejw/elib-base).
