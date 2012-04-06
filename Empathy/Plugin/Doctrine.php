<?php


namespace Empathy\Plugin;
use Empathy\Plugin as Plugin;

class Doctrine extends Plugin implements PreDispatch
{
  
  public function __construct()
  {
    //
  }


  public function onPreDispatch($c)
  {
    if(defined('USE_DOCTRINE') && USE_DOCTRINE == true)
      {
	$this->d_man = \Doctrine_Manager::getInstance();
	$dsn = 'mysql://'.DB_USER.':'.DB_PASS.'@'.DB_SERVER.'/'.DB_NAME;
	$this->d_conn = \Doctrine_Manager::connection($dsn, 'c_'.NAME);
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
	else
	  {
	    \Doctrine::loadModels(DOC_ROOT.'/models');	
	  }    
      }
  }
}
?>