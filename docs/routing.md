

Understanding Routing in Empathy
===


Since inception Empathy uses file-based routing and 
a "best-match" URI parsing algorithm to match "slugged" URIs, with a minimum of one element to a maximum 
of four elements, to a front controller class and event (function).

This matching process produces global GET param variables:

<pre><code class="lang-php">$_GET['module']
$_GET['class']
$_GET['event']
$_GET['id']        # optional
</code></pre>

These params must then resolve in the filesystem within `application` to files with the following structure:

<pre><code class="lang-vim">application/&lt;module&gt;
application/&lt;module&gt;/&lt;class&gt;.php
application/&lt;module&gt;/&lt;class&gt;.php -> A function &lt;event&gt; to call on &lt;class&gt;.php
</code></pre>

Ultimately the goal for any handled route is to match URI slugs to an event (an action) on a controller.  
However, defaults can be used and are implied with routing inherently to make things simpler.

If no module can be resolved the module is set the default module in `/config.yml`.

<pre><code class="lang-yaml">boot_options:
  default_module: front
</code></pre>

If a class cannot be resolved from the URI, the class defaults to a PHP file/class with the same
name as the current module.

<pre><code class="lang-vim">application/front/front.php</code></pre>


If an action cannot be resolved it will default to the action "default_event".

<pre><code class="lang-php">&lt;?php
namespace Empathy\MVC\Controller;

class front extends CustomController
{
    public function default_event()
    {
        $this->assign('centerpage', true);
    }
}
</code></pre>


If a value is found as the last URI slug that can be coerced into a number, it will be set
to `$_GET['id']`.

Examples
---

To invoke the `default_event` event/action on the `front` class (the default class) of the `front` module
all these routes are valid:

<pre><code class="lang-vim">/            # picks up default module in `/config.yml`
/front
/front/front
/front/front/default_event
</code></pre>

With numeric id set to $_GET['id']:

<pre><code class="lang-vim">/front/front/42
/42
</code></pre>


More complex routing
---

If complex routes are required in your app it is recommended that you use 
[Apache Mod Rewrite](https://httpd.apache.org/docs/current/mod/mod_rewrite.html)
for this purpose.  As URIs map to GET params there is limitless flexibility when creating
more custom routing.

For example here is some mod rewrite config, served within an `.htaccess` file for the routing
for a blog based on [elib-blog](/docs/elib-blog).

<pre><code class="lang-vim">RewriteEngine on
RewriteBase /

RewriteRule ^([0-9]{4})/?$ index.php?module=blog&class=blog&event=year&id=$1
RewriteRule ^([0-9]{4})/([a-z]{3})/?$ index.php?module=blog&class=blog&event=month&year=$1&month=$2
RewriteRule ^([0-9]{4})/([a-z]{3})/([0-9]{2})/?$ index.php?module=blog&class=blog&event=day&year=$1&month=$2&day=$3
RewriteRule ^([0-9]{4})/([a-z]{3})/([0-9]{2})/([a-z0-9-]+)/?$ index.php?module=blog&class=blog&event=item&year=$1&month=$2&day=$3&slug=$4&id=0
RewriteRule ^tags/([_a-z0-9-\+]+)/?([0-9]+)?/?$ index.php?module=blog&class=blog&event=tags&active_tags=$1&id=$2
RewriteRule ^tags/?$ index.php?module=blog&class=blog&event=tags
RewriteRule ^set_category/([a-z]+)?/?$ index.php?module=blog&class=blog&event=set_category&category=$1
RewriteRule ^category/([a-z]+)?/?$ index.php?module=blog&class=blog&event=category&category=$1

RewriteRule ^([_a-zA-Z0-9-]/*)*$ index.php
</code></pre>

Next
---
Go to [JSON View Plugin](./json-view-plugin.md).


    
