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
	
	// First we check if the class is the base class needed to initialize the framework(theBox) or any of the other core classes
	if($class == 'thebox') {
		require_once ROOT . DS . 'Core' . DS . 'theBox.php'; 
	}
	elseif($class == 'view') {
		require_once ROOT . DS . 'Core' . DS . 'View.php';
	}
	elseif($class == 'database') {
		require_once ROOT . DS . 'Core' . DS . 'Database.php';
	}
	// If its not the base framework we then check if it is an Object
	elseif(stristr($class,'obj')) {
		$class = substr($class, 4, length($class));
		require_once ROOT . DS . 'Objects' . DS . $class . 'Object.php';
		class_alias(ucwords($class),ucwords($class).'Object');
	}
	elseif(stristr($class,'Controller')) {
		require_once ROOT . DS . 'Controllers' . DS . $class . '.php';
		// Initialize the controller
		if(method_exists($class,'init'))
			$class::init();
	}
	// Otherwise it must be a model or something we dont have to worry about (such as a third party library)
	else {
		$class_name = $class;
		if(!stristr($class,'Model')) {
			$class_name = ucwords($class).'Model';
		}
		if(file_exists(ROOT . DS . 'Models' . DS . $class_name . '.php')) {
			require_once ROOT . DS . 'Models' . DS . $class_name . '.php';
			// Initialize the model
			if(!($class_name === $class))
				class_alias($class_name,ucwords($class));	
			if(method_exists($class,'init'))
				$class::init();
		}		
	}
}
	
spl_autoload_register('theBoxAutoLoad');


// We need to include our third party libraries ourselves and then initialize them however we would want to.
// Using smarty to create a template structure
theBox::load('Lib', 'Smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->setTemplateDir(ROOT . DS . 'Views');
$smarty->setCompileDir(ROOT . DS . 'Views/Compile');
$smarty->setConfigDir(ROOT . DS . 'Libraries/Smarty/Config');
$smarty->setCacheDir(ROOT . DS . 'Cache/Smarty');
// We are using smarty as our template handler but theoretically you could set this to 
// your own, method naming would need to be the same as Smarty
View::init($smarty);

// Load the base configuration file
require_once ROOT . DS . 'Config' . DS . 'core.php';


// Initialize the database if we use it
if(theBox::initDB()) {
	require_once ROOT . DS . 'Config' . DS . 'database.php';
	$db_config = new DB_CONFIG();
	$useThis = theBox::initDB();
	Database::init($db_config->$useThis);
	
	if(theBox::getDebug()) {
		Database::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	else {
		Database::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
	}
}

if(theBox::getDebug()) {
	error_reporting(E_ALL);
}
else {
	error_reporting(0);
}

// And let the routing magic begin (loading the appropriate Controller)
theBox::bootstrap();

// And close DB if we used it
if(theBox::initDB()) {
	Database::close();
}