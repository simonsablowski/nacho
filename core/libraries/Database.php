<?php

class Database {
	protected static $configuration = array();
	protected static $className = NULL;
	protected static $link = NULL;
	protected static $requiredFields = array();
	
	public static function __callStatic($method, $parameters) {
		if (!self::isInitialized()) throw new FatalError('Database not initialized');
		return call_user_func_array(array(self::getClassName(), $method), $parameters);
	}
	
	protected static function isInitialized() {
		return !is_null(self::getClassName());
	}
	
	final public static function initialize($configuration) {
		self::setClassName($configuration['type'] . 'Database');
		
		foreach (self::getRequiredFields() as $field) {
			if (!array_key_exists($field, $configuration)) {
				throw new FatalError('Required fields missing', array('Given fields' => array_keys($configuration), 'Required fields' => self::getRequiredFields()));
			}
			
			self::$configuration[$field] = $configuration[$field];
		}
	}
	
	final public static function getConfiguration($field = NULL) {
		return !is_null($field) ? (isset(self::$configuration[$field]) ? self::$configuration[$field] : NULL) : self::$configuration;
	}
	
	final protected static function getClassName() {
		return self::$className;
	}
	
	final protected static function setClassName($className) {
		return self::$className = $className;
	}
	
	final protected static function getLink() {
		return self::$link;
	}
	
	final protected static function setLink($link) {
		return self::$link = $link;
	}
}