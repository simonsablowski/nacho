<?php

abstract class OdbcDatabase extends SqlDatabase {
	protected static function getError() {
		return odbc_error(self::getLink());
	}
	
	public static function query($statement) {
		if (!$result = odbc_exec(self::getLink(), $statement)) {
			throw new FatalError('Invalid database query', self::getError($result));
		} else {
			return $result;
		}
	}
	
	public static function fetch($result) {
		return odbc_fetch_row($result);
	}
	
	public static function count($result) {
		return odbc_num_rows($result);
	}
	
	public static function escape($value) {
		return addslashes($value);
	}
}