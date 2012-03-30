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

class Database {
	
	protected $DB = false;
	
	public static function init($settings) {
		$host = $settings['host'];
		$dbname = $settings['database'];
		try {
			$DB = new PDO("mysql:host=$host;dbname=$dbname",$settings['login'],$settings['password']);
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
	}
	
	// Handle all database errors however you want here
	protected static function _handleError($e) {
		echo $e->getMessage();
	}
	
}