<?php

class MsAccessDatabase extends OdbcDatabase {
	protected static $requiredFields = array(
		'type',
		'driver',
		'dbq',
		'user',
		'password'
	);
	
	protected static function getRequiredFields() {
		return self::$requiredFields;
	}
	
	public static function connect() {
		$connectionString = sprintf('Driver={%s};Dbq=%s;', self::getConfiguration('driver'), self::getConfiguration('dbq'));
		if (!self::setLink(odbc_connect($connectionString, self::getConfiguration('user'), self::getConfiguration('password')))) {
			throw new FatalError('Cannot connect to database', array('driver' => self::getConfiguration('driver'), 'dbq' => self::getConfiguration('dbq')));
		}
	}
}