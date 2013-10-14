

The MVC
=======

You may or may not be familiar with MVC architectures but after this section
it should all be pretty clear to you and hopefully you will feel confident creating
your own model, controller and view code within Empathy apps.


Models
------

Let's jump straight into the deep end then and create a model.  The first you'll need
to do is create the directory where model files live which is called 'storage'.  (By the way
the convention of having directories labelled 'application', 'storage' and 'presentation'
is a convention with Empathy that goes back to at least 2007.)::

Anyway so within the root directory of your app::

	$ mkdir ./storage

Inside create a file called Hello.php with the following contents::

	<?php

	namespace Empathy\MVC\Model;

	class Hello
	{
	    private $_name;

	    public function __construct($name)
	    {
	        $this->name = $name;
	        if($this->name == 'Mike') {
	            $this->name = 'author';
	        }
	    }
	    private function _getName()
	    {
	        return $this->name;
	    }

	    public function sayHi()
	    {
	        echo 'Hello, '.$this->_getName();
	    }
	}

Let's leave testing this new model for now and move onto the controller.


Controllers
-----------

Controllers live in direcorties under 'application' beneath the root folder. These sub-folders
are known as modules and each module needs to have a class of the same neame beneath it.  To create a new module create the directory 'front' and inside this directory the file 'front.php' with the following code:: 

	<?php

	namespace Empathy\MVC\Controller;

	class front extends CustomController
	{
		public function default_event()
		{
			// do some stuff...
		}
	}

We're going to create a perfectly valid controller because we're going to get this controller to do very little. This is one of the core concepts of Empathy. ALWAYS use 'thin' controllers instead of 'fat' controllers.  This 
means that the functions (or 'actions') inside controller classes should always be only a few lines.  So inside
the default_event function we're going to create an instance of the Hello model and pass it to the view.  This is done like this::



	public function default_event()
	{
		$h = \Empathy\MVC\Model::load('Hello', null, array('Mike'));
		$this->assign('hello', $h);
	}

The key line in this code is the call to 'Model::load'.  The first parameter here is the name of our model class, which resides within the storage directory.  The second argument, which has been set to null, is where an ID may be specified for fetching a specific record from the database with the corresponding ID.  (More on this later.) The last argument is the array of parameters that we want to send to model class as it is instantiated.  In the section after this one we will be looking at more advanced uses of the model and the native MySQL abstraction layer that is built into things called Entity classes.

[to do add a description about the nameing conventions of modules, classes and actions].


Views
-----

Views from the perspective of a web application are simply the rendered templates that store HTML code.  To create a new view we need to create a file called 'front.tpl' inside the directory labelled 'presentation'.  By default, when Empathy displays a view it will look for a template file that has the same name of the current class.  In this case the class is the same name as the module. (The directory the class is within).  If you preferred to have
Empathy always look for a tempate file that has the same name as the current module you can change this setting by
adding an entry to the global config file::

	tpl_by_class: false

However if set to true then this is the default behaviour and it will load templates by class name. For example we might have another controller inside the 'front' module called 'other.php'.  When 'tpl_by_class' is set to true (or not specified) the view empathy will look for to load will be called 'other.tpl'.

To choose to load a custom-named template it is done explicitly in our action.  This is through using the setTempalte function (which belongs to controller classes or more specifically the parent of Controller classes). E.g inside our 'default_event' action we could have this code::

	$h = \Empathy\MVC\Model::load('Hello', null, array('Mike'));
	$this->assign('hello', $h);
	$this->setTempalte('my_other_template.tpl');

If you expect to be using the same custom named template lots of times within the same controller then the
following is recmmended solution where the template is assigned in the constructor and so will be set just before any actions 
are exectued.::


	<?php 

	namespace Empathy\MVC\Controller;

	class my_class
	{
		public function __construct($boot)
		{
			parent::__construct($boot);
			$this->setTempate('custom.tpl');
		}


		public function default_event()
		{
			// will attempt to render the 'custom.tpl' template
		}
	}

If you decied that any of these actions needs to use something different you can
always call setTemplate again to override the selection made in the constructor. What if you don't want to render a view, you might be wondering?  In some cases this can be desired and it can be achieved by simply returning false at the end of the method::

	return false; // don't attempt to render anything


In the case of our app though, we want to do something with the 'Hello' model that is being passed to the view
by the controller.  Let's try calling the 'sayHello' method which is native to our model object inside
our 'front.tpl' template file::


	{$hello->sayHello()}

Load up the URL http://localhost/firstapp/public_html/front and you should see the message 'hello, author'.

For more information on what you can do with Smarty templates see http://smartypants.com


















