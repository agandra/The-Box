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
	
	protected static $DB = false;
	protected static $temp = array();
	protected static $i = 1;
	
	public static function init($settings) {
		$host = $settings['host'];
		$dbname = $settings['database'];
		try {
			self::$DB = new PDO("mysql:host=$host;dbname=$dbname",$settings['login'],$settings['password']);
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
	}
	
	public static function setAttribute($attribute, $value) {
		self::$DB->setAttribute($attribute, $value);
	}
	
	public static function prepare($query) {
		self::$temp[self::$i] = self::$DB->prepare($query);
		$temp = new DBO_TEMP();
		$temp->init(self::$i);
		self::$i++;
		
		return $temp->returnSelf();
	}
	
	public static function execute($place) {
		try {
			self::$temp[$place]->execute();
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
	}
	
	public static function qInsert($table, $data) {
		$fields = implode(', ', array_keys($data));
		$placeholders = implode(', ', array_map('self::_addcolon',array_keys($data)));
		$query = 'INSERT INTO '.$table.' ('.$fields.') value ('.$placeholders.')';
		
		try{
			$STH = self::$DB->prepare($query);
			$STH->execute($data);
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
	}
	
	protected static function _addColon($string) {
		$string = ':'.$string;
		return $string;
	}
	// Close the DB connection
	public static function close() {
		self::$DB = null;
	}
	
	// Handle all database errors however you want here
	protected static function _handleError($e) {
		echo $e->getMessage();
	}
	
}

class DBO_TEMP {
	private $i = false;
	
	public function init($i) {
		$this->i = $i;
	}
	
	public function returnSelf() {
		return $this;
	}
	
	public function execute() {
		if($this->i) {
			Database::execute($this->i);
		}
	}
}