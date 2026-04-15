

ORM / Storage
===


Design Philosophy
---

Empathy’s ORM is intentionally minimal:

* No heavy abstractions
* No hidden queries
* Full control remains with the developer

This keeps applications simple, predictable, and easy to debug.


What is this ORM?
---

Empathy’s ORM is a lightweight Active Record implementation:

* One PHP class = one database table
* Public properties map directly to table columns
* Minimal abstraction over SQL (no query builder)
* You are encouraged to write custom logic where needed

This is **not** a full-featured ORM like Doctrine—simplicity and control are prioritised.

Quick Example
---

<pre><code class="lang-php">$c = Model::load(Comment::class);

// create
$c->name = 'John';
$c->comment = 'Hello world';
$id = $c->insert(['comment']);

// read
$c->load($id);

// update
$c->comment = 'Updated';
$c->save();

// delete
$c->delete();
</code></pre>


Core Entity Methods
---

* `load(int $id)` – populate the entity from the database
* `insert(array $escapeFields = [])` – insert a new row
* `save()` – update an existing row
* `delete()` – delete the row
* `validates()` – override to define validation rules

Database setup and files
---
As outlined in [elib-base - getting started](/docs/elib-base/docs/getting-started.md), 
there are two significant database files in any Empathy project.

* setup.sql
* inserts.sql
<p>&nbsp;</p>


`setup.sql` is for data definition and `inserts.sql` is for data manipulation - i.e. 
for inserting basic database fixtures via `INSERT` statements.

We will now go through an example based on this website where there is a "comment" table and entity type.
For simplicity, this example focuses on a single `comment` table.

`/setup.sql`:

<pre><code class="lang-sql">DROP DATABASE IF EXISTS project;
CREATE DATABASE project;
USE project;

DROP TABLE IF EXISTS comment;

CREATE TABLE comment (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NULL,
    doc VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(255) NULL,
    comment TEXT NOT NULL,
    submitted TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(255) NULL,
    FOREIGN KEY (user_id) REFERENCES user (id)
) ENGINE = InnoDB;
</code></pre>

`/inserts.sql`: No database fixtures are required.


Model
---
The next thing is wiring up a basic model for this entity type.

For this purpose, it will be necessary to create an autoload directory within the 
application for arbitrary source files related to the project.  This can be achieved by
modifying your `composer.json` file:


<pre><code class="lang-json">"autoload": {
        "psr-0": { "EP": "src/" }
    }
</code></pre>

NB: Here in this example "EP" stands for "Empathy Project" but it could be any name you like.

Once adding this entry you should regenerate the composer autoload files:


<pre><code class="lang-shell">composer dump-autoload
</code></pre>

We now  come to the model class itself which can live within `/src/EP/Model/Comment.php`.
A model class represents a single database table.

<pre><code class="lang-php">&lt;?php
declare(strict_types=1);

namespace EP\Model;

use Empathy\MVC\Validate;
use Empathy\MVC\Entity;
use Empathy\MVC\Session;
use Empathy\MVC\DI;

class Comment extends Entity
{
    public int $id;
    public $user_id;
    public $doc;
    public $name;
    public $url;
    public $comment;
    public $submitted;
    public $title;

    private $user_name;
    private $user_url;
    private $captcha_phrase;


    const TABLE = 'comment';

    public function validates() {
        if ($this->doValType(Validate::TEXT, 'doc', preg_replace('/[\/\.-]/', '', $this->doc), false)) {
             if (!DI::getContainer()->get('CurrentUser')->canUseName($this->name)) {
                 $this->addValError('Reserved name. Try logging in first.', 'name');
             }
        }
        $this->doValType(Validate::TEXT, 'name', $this->name, false);
        $this->doValType(Validate::URL, 'url', $this->url, true);
        $this->doValType(Validate::TEXT, 'submitted', $this->submitted, false);

        if ($this->comment == "") {
            $this->addValError('Comment is required.', 'comment');
        }

        if (!DI::getContainer()->get('CurrentUser')->loggedIn()) {
            if ($this->captcha_phrase !== Session::get('captcha_phrase')) {
                $this->addValError('Captcha invalid.', 'captcha_phrase');
            }
        }
    }

    public function setCaptchaPhrase($phrase) {
        $this->captcha_phrase = $phrase;
    }

    public function setUserName($user_name) {
        $this->user_name = $user_name;
    }

    public function setUserUrl($user_url) {
        $this->user_url = $user_url;
    }

    public function getUserName() {
        return $this->user_name;
    }
    public function getUserUrl() {
        return $this->user_url;
    }
}
</code></pre>

Here a few points to bear in mind when creating model classes, reflected in the above:

* The class extends Empathy\MVC\Entity
* There is a class constant `TABLE` that refects the table name used in `setup.sql`
  (and also `inserts.sql` if used)
* The table has public $id property with strict int type for the tables auto-incrementing
  id field.
* The class has publicly scoped properties for each of the defined table fields in `setup.sql`
* Properties that are not database columns should be:
  * declared `private`
  * accessed via getters/setters
These are not persisted to the database.

  

Loading a record
---
Loading a database object is achieved by first loading the database model using Empathy\MVC\Model
and then calling `load` with the corresponding id.

`Model::load()` returns an instance of the model class, wired to the ORM.

The subsequent `$entity->load($id)` call fetches the database row into that instance.

<pre><code class="lang-php">$c = Model::load(Comment::class);
$c->load($_GET['id']);
</code></pre>


Inserting a record
---

Do not set the `id` field manually — it is auto-incremented by the database.
The array passed to `insert()` defines which fields should be HTML-escaped before being stored in the database.
This is typically used for user-generated content.

Example:
<pre><code class="lang-php">$id = $c->insert(['comment']);
</code></pre>


<pre><code class="lang-php">if (isset($_POST['submit']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === Session::get('csrf_token')) {
    $c->submitted = true;
    $c->doc = $docPath;

    if ($user->isLoggedIn()) {
        $c->user_id = $user->getUserId();
    }

    $c->name = $c->getUserName() !== '' ? $c->getUserName() : $_POST['name'];
    $c->url = $c->getUserUrl() !== '' ? $c->getUserUrl() : $_POST['url'];

    $url_trimmed = preg_replace('/^https?:\/\//', '', $c->url);
    $c->url = $c->url !== '' ? 'https://' . $url_trimmed : '';

    $c->comment = $_POST['comment'];
    $captchaPhrase = $_POST['captcha_phrase'] ?? '';
    $c->setCaptchaPhrase($captchaPhrase);

    $c->validates();

    if ($c->hasValErrors()) {
        $this->assign('errors', $c->getValErrors());
        $c->url = $url_trimmed;
    } else {
        $id = $c->insert(['comment']);
        $this->redirect($docPath . '#comment-' . $id);
    }
}
</code></pre>

Updating a record
---

<pre><code class="lang-php">$c = Model::load(Comment::class);
$c->load($_GET['id']);
$c->comment = 'This comment has been overwritten.';
$c->save();
</code></pre>


Deleting a record
---

<pre><code class="lang-php">$c = Model::load(Comment::class);
$c->load($_GET['id']);
$c->delete();
</code></pre>


Validation
---

Override `validates()` to define validation rules.

Use helper methods like:

* `doValType()` – built-in validation checks
* `addValError()` – manually add an error

After calling `$entity->validates()`:

* `$entity->hasValErrors()` – check for errors
* `$entity->getValErrors()` – retrieve them


Relationships
---

Empathy does not provide built-in relationship mapping (e.g. belongsTo, hasMany).

You are encouraged to:

* load related entities manually
* implement helper methods in your models where needed


Custom behaviour
---

Add custom behaviour directly to your model classes via methods.
There are other utility methods in Empathy\MVC\Entity, for example retrieving
basic data with pagination.  It's a good idea to familiarise yourself with the methods in that class.


Security Notes
---

* Always validate user input via `validates()`
* Use CSRF tokens for form submissions
* Escape user-generated content via `insert()`


Next
---
Go to [JSON View Plugin](./json-view-plugin.md).


    
