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
	
	protected $debug = 2;
	protected $controller = '';
	protected $action = '';
	protected $home = false;
	protected $database = true;
	
	public function setDebug($debug) {
		if(is_int($debug)) {
			$this->debug = $debug;
		}
		else {
			print 'Your debug value in the core config file is set incorrectly';
			exit(1);
		}
	}
	
	public function getDebug() {
		return $this->debug;
	}

	public function setHome($home) {
		if(is_array($home) && $home['controller'] && $home['action']) {
			$this->home = $home;
		}
	}
	
	public function useDatabase($database) {
		if(!$database) {
			$this->database = false;
		}
	}
	
	public function _setRoute() {
		$path = false;
		$params = false;
		
		// This needs work, so it can work on more servers and more fallbacks
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
			$params = $this->_cleanRoute($params);
		}
		
		if(!is_array($params)) {
			$this->controller = '';
		}
		else {
			$this->controller = $params[1];
			if($params[2]) {
				$this->action = $params[2];
			}
		}		
		$this->_validateRoute();
	}
	
	public function _cleanRoute($params) {
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
		
		return $params;
	}
	// This makes sure that controller and action are within the expected values (prevent users going to restricted areas)
	// Will 404 users if these values are not valid
	public function _validateRoute() {
		
	}
	
	public function bootstrap() {
		$this->_setRoute();
		
		if($this->controller == '') {
			if(!$this->home) {
				print 'Your home value in the core config file is set incorrectly';
				exit(1);
			}
			
			$this->controller = $this->home['controller'];
			$this->action = $this->home['action'];	
		}

		$class = ucwords(strtolower($this->controller)).'Controller';
		
		if(file_exists(ROOT . DS . 'Controllers' . DS . $class . '.php')) {
			$action = $this->action;
			if($action === '') {
				$action = 'index';
			}	
			if(method_exists($class,$action)) {
				$class::$action();
			}
			else {
				$this->send404();
			}
		}
		else {
			$this->send404();
		}

	}
	
	public function send404() {
		echo '404ing hard';
	}
}