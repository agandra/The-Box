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
	protected $controller = '_index';
	protected $action = '';
	
	public function setDebug($debug) {
		if(is_int($debug)) {
			$this->debug = $debug;
		}
		else {
			return false;
		}
	}
	
	public function getDebug() {
		return $this->debug;
	}

	public function _setRoute() {
		$path = false;
		$params = false;
		
		// This needs work, so it can work on more servers and more fallbacks
		if(isset($_SERVER['argv'][0])) {
			$path = $_SERVER['argv'][0];
		}
		elseif(isset($_ENV['argv'][0])) {
			$path = $_ENV['argv'][0];
		}
		
		if($path) {
			$params = explode('/', $path);
		}
		
		if(!is_array($params)) {
			$this->controller = '_index';
		}
		else {
			$this->controller = $params[1];
			if($params[2]) {
				$this->action = $params[2];
			}
		}
	}
	
	public function bootstrap() {
		$this->_setRoute();
	}
}