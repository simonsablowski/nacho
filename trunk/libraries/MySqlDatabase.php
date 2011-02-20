<?php

class MySqlDatabase extends SqlDatabase {
	public static function connect() {
		if (!self::setLink(mysql_connect(self::getConfiguration('host'), self::getConfiguration('user'), self::getConfiguration('password')))) {
			throw new FatalError('Cannot connect to database host', self::getConfiguration('host'));
		}
		
		if (!mysql_select_db(self::getConfiguration('name'), self::getLink())) {
			throw new FatalError('Cannot select database', self::getConfiguration('name'));
		}
	}
	
	protected static function getError() {
		return mysql_error(self::getLink());
	}
	
	public static function query($statement) {
		if (!$result = mysql_query($statement)) {
			throw new FatalError('Invalid database query', self::getError());
		} else {
			return $result;
		}
	}
	
	public static function fetch($result) {
		return mysql_fetch_assoc($result);
	}
	
	public static function count($result) {
		return mysql_num_rows($result);
	}
	
	public static function escape($value) {
		return mysql_real_escape_string($value);
	}
}