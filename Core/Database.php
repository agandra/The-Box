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
			return new DBO_TEMP();
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
	}
	
	public static function setAttribute($attribute, $value) {
		self::$DB->setAttribute($attribute, $value);
	}
	
	
	public function lastInsertId() {
		$id = false;
		try {
			$id = self::$DB->lastInsertId();
			return $id;
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
		
	}
	
	public static function prepare($query) {
		try {
			self::$temp[self::$i] = self::$DB->prepare($query);
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
		
		$temp = new DBO_TEMP();
		$temp->init(self::$i);
		self::$i++;
		
		return $temp->returnSelf();
	}
	
	public static function fetchAll($query) {
		try {
			$fetch = self::$DB->query($query);
			return $fetch->fetchAll(PDO::FETCH_ASSOC);		
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
	}
	
	public static function query($query) {
		try {
			return self::$DB->query($query);		
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
	}
	
	public static function exec($query) {
		try {
			self::$DB->exec($qeuery);
		}
		catch(PDOException $e) {
			self::_handleError($e);
		}
	}
	
	public static function execute($place, $data) {
		try {
			return self::$temp[$place]->execute($data);
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
	// Make sure your debug mode is set to what you want
	protected static function _handleError($e) {
		echo $e->getMessage();
	}
	
}

class DBO_TEMP {
	public $i = false;
	
	public function init($i) {
		$this->i = $i;
	}
	
	public function returnSelf() {
		return $this;
	}
	
	public function execute($data) {
		if($this->i) {
			Database::execute($this->i, $data);
		}
	}
}