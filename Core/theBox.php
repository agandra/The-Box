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

/**
* The purpose of this class is to initialize all the necessary elements of the
* framework and provide a basis to configure the framework to the users liking.
*/

class theBox {
	
	protected static $debug = 2;
	protected static $controller = '';
	protected static $action = '';
	protected static $home = false;
	protected static $database = false;
	
	/**
	* This is used inside the core config file to set the debug state.
	*/
	public static function setDebug($debug) {
		if(is_int($debug)) {
			self::$debug = $debug;
		}
		else {
			print 'Your debug value in the core config file is set incorrectly';
			exit(1);
		}
	}
	
	/**
	* This can be called from anywhere to get the current debug level.
	* It is used in the main index.php file - to set error handling (for now)
	*/
	public static function getDebug() {
		return self::$debug;
	}

	/**
	* This is used inside the core config file to set the home controller/action.
	*/
	public static function setHome($home) {
		if(is_array($home) && $home['controller'] && $home['action']) {
			self::$home = $home;
		}
	}
	
	/**
	* This is used inside the core config file to set if this application needs Database support.
	*/
	public static function useDatabase($database) {
		self::$database = $database;
	}
	
	/**
	* This is used to check if database support is needed, and what schema to connect to.
	*/
	public static function initDB() {
		return self::$database;
	}
	
	/**
	* This is used to determine the controller and action that should be loaded
	*/
	public static function _setRoute() {
		$path = false;
		$params = false;
		
		// This needs work, so it can work on more servers and more fallbacks.
		// Also needs more testing.
		if(isset($_SERVER['argv'][0])) {
			$path = $_SERVER['argv'][0];
		}
		elseif(isset($_SERVER['REQUEST_URI']) && isset($_SERVER['PHP_SELF'])) {
			$uri = str_replace('/index.php', '', $_SERVER['PHP_SELF']);
			$path = str_replace($uri, '', $_SERVER['REQUEST_URI']);
		}
		elseif(isset($_ENV['argv'][0])) {
			$path = $_ENV['argv'][0];
		}
		
		if($path && ($path != '/')) {
			$params = explode('/', $path);
			$params = self::_cleanRoute($params);
		}
		
		if(!is_array($params)) {
			self::$controller = '';
		}
		else {
			self::$controller = $params[1];
			if(isset($params[2])) {
				self::$action = $params[2];
			}
		}
		
		self::_validateRoute();		
	}
	
	/**
	* We need to make sure that controllers and actions dont have any extra
	* information inside them that they might disrupt proper file loading.
	* Like if there are get values being passed we need to not include them.
	*/
	public static function _cleanRoute($params) {
		foreach($params as &$route) {
			if(stristr($route, '?')) {
				$real_value = explode('?',$route);
				$route = $real_value[0];
			}
			if(stristr($route, '&')) {
				$real_value = explode('&',$route);
				$route = $real_value[0];
			}
		}
		unset($route);
		
		return $params;
	}
	
	/**
	* Set the controller and action to home (defined in the core config file).
	*/
	public static function goHome() {
		if(!self::$home) {
			print 'Your home value in the core config file is set incorrectly';
			exit(1);
		}

		self::$controller = self::$home['controller'];
		self::$action = self::$home['action'];
	}
	
	/**
	* This makes sure that controller and action are within the expected values 
	* (prevent users going to restricted areas).
	* Will set values to 404 if not set correctly
	*/
	public static function _validateRoute() {
		if(self::$controller === '') {
			self::goHome();	
		}
		else {
			// Need to do pattern matching and other stuff here
			// Set controller and action to 404 if isnt proper inputs
		}
		
		self::$controller = ucwords(strtolower(self::$controller));
		$class = self::$controller.'Controller';
		
		if(file_exists(ROOT . DS . 'Controllers' . DS . $class . '.php')) {
			if(self::$action === '') {
				self::$action = 'index';
			}	
			if(!method_exists($class,self::$action)) {
				self::setError('404');
			}
		}
		else {
			self::setError('404');
		}
	}
	
	/**
	* The function that calls other functions to set the controllers and actions
	* and then finally calls the proper method and sets up template parsing.
	*/
	public static function bootstrap() {
		self::_setRoute();

		$class = self::$controller.'Controller';
		$action = self::$action;

		// Double check just incase error handling wasnt set up properly - this is last fail check we kill app otherwise
		// This should never fail unless user deleted/changed ErrorController file
		if(method_exists($class, $action)) {
			$class::$action();
		}
		else {
			print 'Something went wrong please contact server administrator';
			exit(1);
		}
		
		if($class::compile() === true) {
			View::compileView($class::getLayout(), self::$controller, self::$action);
		}	
	}

	/**
	* Set the controller and action as an error state
	*/
	public static function setError($action) {
		self::$controller = 'Error';
		self::$action = 'error_'.$action;
	}
	
	/**
	* A helper to include files, so we can call on them from anywhere within the application.
	*/
	public static function load($type, $file) {
		if(strtolower($type) === 'lib') {
			require_once ROOT . DS . 'Libraries' . DS . $file;
		}
	}

}