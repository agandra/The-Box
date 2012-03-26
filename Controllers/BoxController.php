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

abstract class BoxController {
	
	protected static $compile = true;
	protected static $layout = false;
	
	public static function setCompile($compile) {
		self::$compile = $compile;
	}
	
	public static function compile() {
		return self::$compile;
	}
	
	public static function setLayout($layout) {
		self::$layout = $layout;
	}
	
	public static function getLayout() {
		return self::$layout;
	}
}