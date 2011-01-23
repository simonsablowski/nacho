<?php

class PostgreSqlDatabase extends SqlDatabase {
	public static function connect() {
		$connectionString = 'host=%s dbname=%s user=%s password=%s';
		if (!self::setLink(pg_connect(sprintf($connectionString, self::getConfiguration('host'), self::getConfiguration('name'), self::getConfiguration('user'), self::getConfiguration('password'))))) {
			throw new FatalError('Cannot connect to database', array('host' => self::getConfiguration('host'), 'name' => self::getConfiguration('name')));
		}
	}
	
	protected static function getError($result) {
		return pg_result_error($result);
	}
	
	public static function query($statement) {
		if (!$result = pg_query(self::getLink(), $statement)) {
			throw new FatalError('Invalid database query', self::getError($result));
		} else {
			return $result;
		}
	}
	
	public static function fetch($result) {
		return pg_fetch_result($result);
	}
	
	public static function count($result) {
		return pg_num_rows($result);
	}
	
	public static function escape($value) {
		return pg_escape_string(self::getLink(), $value);
	}
}