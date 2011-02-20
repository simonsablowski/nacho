<?php

class MsSqlDatabase extends OdbcDatabase {
	protected static $requiredFields = array(
		'type',
		'driver',
		'server',
		'name',
		'user',
		'password'
	);
	
	protected static function getRequiredFields() {
		return self::$requiredFields;
	}
	
	public static function connect() {
		$connectionString = sprintf('Driver={%s};Server=%s;Database=%s;', self::getConfiguration('driver'), self::getConfiguration('server'), self::getConfiguration('name'));
		if (!self::setLink(odbc_connect($connectionString, self::getConfiguration('user'), self::getConfiguration('password')))) {
			throw new FatalError('Cannot connect to database', array('driver' => self::getConfiguration('driver'), 'server' => self::getConfiguration('server'), 'name' => self::getConfiguration('name')));
		}
	}
}