<?php /* Smarty version 2.6.18, created on 2008-03-27 14:50:29
         compiled from /var/www/localhost/htdocs/lib/php/empathy/presentation/empathy_error.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', '/var/www/localhost/htdocs/lib/php/empathy/presentation/empathy_error.tpl', 9, false),)), $this); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "empathy_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


<h1>Uh Oh</h1>
<p>This web application has encountered an error. Details follow...</p>

<div id="error">
<p><strong>Occurring on:</strong> <?php echo ((is_array($_tmp=$this->_tpl_vars['app_error']['3'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%y at %H:%M:%S") : smarty_modifier_date_format($_tmp, "%d/%m/%y at %H:%M:%S")); ?>
</p>
<p><strong>In module:</strong> <?php echo $this->_tpl_vars['app_error']['0']; ?>
</p>
<p><strong>In class:</strong> <?php echo $this->_tpl_vars['app_error']['1']; ?>
</p>
<p><strong>Message:</strong> <?php echo $this->_tpl_vars['app_error']['2']; ?>
</p>
</div>
<p class="retry"><a href="http://<?php echo $this->_tpl_vars['failed_uri']; ?>
">Retry</a></p>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "empathy_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>