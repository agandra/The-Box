<?php
/**
 * PHP 5.3.3+
 *
 * The Box
 * Copyright 2012, Near The Box (www.nearthebox.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Near The Box (www.nearthebox.com)
 * @link          http://github.com/agandra/The-Box  The Box PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('VIEW', 'View');
define('WEBROOT', ROOT . DS . VIEW . DS);

function theBoxAutoLoad($class){
	$class = strtolower($class);
	
	// First we check if the class is the base class needed to initialize the framework(theBox)
	if($class == 'thebox') {
		require_once ROOT . DS . 'Core' . DS . 'theBox.php'; 
	}
	// If its not the base framework we then check if it is an Object
	elseif(stristr($class,'obj')) {
		$class = substr($class, 5, length($class));
		require_once ROOT . DS . 'Objects' . DS . $class . 'Object.php';
	}
	elseif(stristr($class,'Controller')) {
		require_once ROOT . DS . 'Controllers' . DS . $class . '.php';
		// Initialize the controller
		if(method_exists($class,'init'))
			$class::init();
	}
	// Otherwise it must be a model or something we dont have to worry about (such as a third party library)
	else {
		if(file_exists(ROOT . DS . 'Models' . DS . $class . 'Model.php')) {
			require_once ROOT . DS . 'Models' . DS . $class . 'Model.php';
			// Initialize the model
			if(method_exists($class,'init'))
				$class::init();
		}		
	}
}
	
spl_autoload_register('theBoxAutoLoad');


// We need to include our third party libraries ourselves and then initialize them however we would want to
// Using smarty to create a template structure
require_once ROOT . DS . 'Libraries' . DS . 'Smarty/Smarty.class.php';
$smarty = new Smarty;
$smarty->setTemplateDir(ROOT . DS . 'Views');
$smarty->setCompileDir(ROOT . DS . 'Views/Compile');
$smarty->setConfigDir(ROOT . DS . 'Libraries/Smarty/Config');
$smarty->setCacheDir(ROOT . DS . 'Cache/Smarty');


// Create an instance of the class needed to run the framework.  We should never create another instance of this class
// Maybe make this a static class later?
$theBox = new theBox();
$theBox->setTemplateHandler($smarty);

// Load the base configuration file
require_once ROOT . DS . 'Config' . DS . 'core.php';

if($theBox->getDebug()) {
	error_reporting(E_ALL);
}
else {
	error_reporting(0);
}

// And let the routing magic begin (loading the appropriate Controller)
$theBox->bootstrap();