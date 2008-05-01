<?php /* Smarty version 2.6.18, created on 2008-03-28 12:09:50
         compiled from /var/www/localhost/htdocs/lib/php/empathy/presentation/empathy.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "empathy_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


<?php if ($this->_tpl_vars['class'] == 'demo'): ?>

<h2>Countries and Country Codes (ISO 3166)</h2>
<ul>
<?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['i']):
?>
<li><?php echo $this->_tpl_vars['i']; ?>
 (<?php echo $this->_tpl_vars['k']; ?>
)</li>
<?php endforeach; endif; unset($_from); ?>
</ul>

<?php else: ?>

<h1>My framework, Empathy</h1>
<p>Empathy is a lightweight MVC framework for developing applications in PHP5 greatly inspired by Joe Stumps articles on the subject found
<a href="http://www.onlamp.com/pub/a/php/2005/09/15/mvc_intro.html">here</a>.  The project has come about through an urge to have in place an MVC type platform that I can understand, while moving my attentions into investigating other web development technologies including other frameworks. Empathy is not meant to be the ultimate solution. Merely what works for me and maybe you too, to some extent.</p>

<h2>Conventions</h2>
<ul>
<li>...</li>
</ul>

<h2>Demos</h2>
<ul>
<li>General demo. Access a list of countries parsed from a text file. (As if accessing a database entity type class.)
<a href="http://<?php echo $this->_tpl_vars['WEB_ROOT']; ?>
<?php echo $this->_tpl_vars['PUBLIC_DIR']; ?>
/empathy/demo">Go</a></li>
<li>Simulate an error. <a href="http://<?php echo $this->_tpl_vars['WEB_ROOT']; ?>
<?php echo $this->_tpl_vars['PUBLIC_DIR']; ?>
/empathy/demo/sim_error">Go</a></li>
</ul>

<?php endif; ?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "empathy_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>