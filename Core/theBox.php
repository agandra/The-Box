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
		$path = $_SERVER['REQUEST_URI'];
		$params = explode('/', $path);
		
		if(!is_array($params)) {
			$this->controller = 'index';
		}
	}
	
	public function boostrap() {
		$this->_setRoute();
	}
}