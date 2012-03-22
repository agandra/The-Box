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

// Remove this and make error part of thebox framework
error_reporting(E_ALL);

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
	// If its not the base framework we then check if it is either an Object or a Library
	elseif(stristr($class,'obj')) {
		$class = substr($class, 5, length($class));
		require_once ROOT . DS . 'Objects' . DS . $class . '.php';
	}
	elseif(stristr($class,'lib')) {
		$class = substr($class, 5, length($class));
		require_once ROOT . DS . 'Libraries' . DS . $class . '.php';
	}
	// Otherwise it must be a model
	else {
		require_once ROOT . DS . 'Models' . DS . $class . '.php';
		// Initialize the model
		if(method_exists($class,'init'))
			$class::init();
	}
}
	
spl_autoload_register('theBoxAutoLoad');

$theBox = new theBox();

$theBox->init();

