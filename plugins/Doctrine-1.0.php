<?php

namespace Empathy\Plugin;
use Empathy\Plugin as Plugin;

class Doctrine extends Plugin implements PreDispatch
{
  private $d_conn;
  private $d_man;

  public function __construct()
  {
    spl_autoload_register(array($this, 'loadModel'));
  }

  public function loadModel($class)
  {
    $location = array(
		      DOC_ROOT.'/models',
		      DOC_ROOT.'/models/generated'		
		      );
    $load_error = 1;
    $i = 0;
    while($i < sizeof($location) && $load_error == 1)
      {
	$class_file = $location[$i].'/'.$class.'.php';           	
	if(@include($class_file))
	  {		
	    $load_error = 0;
	  }
	$i++;
      }
  }
 
  private function isIP($server)
  {
    $ip = false;
    $count = 0;
    $stripped = str_replace('.', '', DB_SERVER, $count);
    if($count)
      {	
	if(is_numeric($stripped))
	  {
	    $ip = true;
	  }	
      }
    return $ip;
  }
  

  public function onPreDispatch($c)
  {       
    if(!$this->isIP(DB_SERVER))
      {	   
	throw new \Empathy\Exception('Database server must be an IP address.');
      }	            
            
    $dsn = 'mysql://'.DB_USER.':'.DB_PASS.'@'.DB_SERVER.'/'.DB_NAME;
    $this->d_conn = \Doctrine_Manager::connection($dsn, 'c_'.NAME);

    $this->d_man = \Doctrine_Manager::getInstance();
    $this->d_man->setAttribute(\Doctrine::ATTR_VALIDATE, \Doctrine::VALIDATE_ALL);
    $this->d_man->setAttribute(\Doctrine::ATTR_EXPORT, \Doctrine::EXPORT_ALL);
    $this->d_man->setAttribute(\Doctrine::ATTR_MODEL_LOADING, \Doctrine::MODEL_LOADING_CONSERVATIVE);
    $this->d_man->setAttribute(\Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
    
    
    if(isset($_SERVER['argc']) && $_SERVER['argc'] > 1)
      {
	switch($_SERVER['argv'][1])
	  {
	  case 'doctrine_models':
	    \Doctrine::generateModelsFromDb(DOC_ROOT.'/models', array('c_'.NAME), array('generateTableClasses' => true));
	    exit(1);
	    break;
	  case 'doctrine_yaml':
	    \Doctrine::generateYamlFromModels(DOC_ROOT.'/schema.yml', DOC_ROOT.'/models');
	    exit(1);
	    break;
	  case 'doctrine_generate':
	    \Doctrine::dropDatabases();
	    \Doctrine::createDatabases();
	    \Doctrine::generateModelsFromYaml(DOC_ROOT.'/schema.yml', DOC_ROOT.'/models');
	    \Doctrine::createTablesFromModels(DOC_ROOT.'/models');		
	    exit(1);
	    break;
	  default:
	    die('No valid command line operation specified.'."\n");
	    break;
	  }	    
      }

    if(file_exists(DOC_ROOT.'/models'))
      {
	\Doctrine::loadModels(DOC_ROOT.'/models');	
      }
  }
}
?>