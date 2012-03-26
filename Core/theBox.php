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
	protected $templateHander = false;
	
	public function setTemplateHandler($handler) {
		$this->templateHandler = $handler;
	}
	
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
			if(isset($params[2])) {
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
		unset($route);
		
		return $params;
	}
	
	public function goHome() {
		if(!$this->home) {
			print 'Your home value in the core config file is set incorrectly';
			exit(1);
		}

		$this->controller = $this->home['controller'];
		$this->action = $this->home['action'];
	}
	
	// This makes sure that controller and action are within the expected values (prevent users going to restricted areas)
	// Will set values to 404 if not set correctly
	public function _validateRoute() {
		if($this->controller === '') {
			$this->goHome();	
		}
		else {
			// Need to do pattern matching and other stuff here
			// Set controller and action to 404 if isnt proper inputs
		}
		
		$this->controller = ucwords(strtolower($this->controller));
		$class = $this->controller.'Controller';
		
		if(file_exists(ROOT . DS . 'Controllers' . DS . $class . '.php')) {
			if($this->action === '') {
				$this->action = 'index';
			}	
			if(!method_exists($class,$this->action)) {
				$this->setError('404');
			}
		}
		else {
			$this->setError('404');
		}
	}
	
	public function bootstrap() {
		$this->_setRoute();

		$class = $this->controller.'Controller';
		$action = $this->action;

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
			$this->compileView($class::getLayout());
		}	
	}
	
	public function compileView($layout) {
		if(!$this->templateHandler) {
			print 'Template Handler not set up properly';
			exit(1);
		}
		
		if(!$layout) {
			$layout = 'Default';
		}
		
		$layout_dir = 'Global/'.$layout.'/';
		echo(
			$this->templateHandler->fetch($layout_dir.'header.tpl') .
			$this->templateHandler->fetch($this->controller.'/'.$this->action.'.tpl') .
			$this->templateHandler->fetch($layout_dir.'footer.tpl')
			);
	}
	
	public function setError($action) {
		$this->controller = 'Error';
		$this->action = 'error_'.$action;
	}

}