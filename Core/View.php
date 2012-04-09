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

class View {
	
	protected static $templateHandler = false;
	
	public static function init($handler) {
		self::$templateHandler = $handler;
	}
	
	public static function compileView($layout, $controller, $action) {
		if(!self::$templateHandler) {
			print 'Template Handler not set up properly';
			exit(1);
		}
		
		if(!$layout) {
			$layout = 'Default';
		}
		
		if(!file_exists(ROOT . DS . 'Views' . DS . $controller . DS . $action . '.tpl')) {
			self::_fileMissing($layout);
		}
		$layout_dir = 'Global/'.$layout.'/';
		echo(
			self::$templateHandler->fetch($layout_dir.'header.tpl') .
			self::$templateHandler->fetch($controller.'/'.$action.'.tpl') .
			self::$templateHandler->fetch($layout_dir.'footer.tpl')
			);
	}
	
	public static function _fileMissing($layout) {
		ErrorController::error_missing_view();
		
		if(!$layout) {
			$layout = 'Default';
		}
		
		$layout_dir = 'Global/'.$layout.'/';
		
		echo(
			self::$templateHandler->fetch($layout_dir.'header.tpl') .
			self::$templateHandler->fetch('Error/error_missing_view.tpl') .
			self::$templateHandler->fetch($layout_dir.'footer.tpl')
			);
	}
	
	public static function render($file) {
		echo self::$templateHandler->fetch($file);
	}
	
	public static function assign($name, $value) {
		self::$templateHandler->assign($name, $value);
	}
	
}