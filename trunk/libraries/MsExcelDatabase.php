<?php

class MsExcelDatabase extends OdbcDatabase {
	protected static $requiredFields = array(
		'type',
		'driver',
		'driverId',
		'dbq',
		'defaultDirectory',
		'user',
		'password'
	);
	
	protected static function getRequiredFields() {
		return self::$requiredFields;
	}
	
	public static function connect() {
		$connectionString = sprintf('Driver={%s};DriverId=%d;Dbq=%s;DefaultDir=%s;', self::getConfiguration('driver'), self::getConfiguration('driverId'), self::getConfiguration('dbq'), self::getConfiguration('defaultDirectory'));
		if (!self::setLink(odbc_connect($connectionString, self::getConfiguration('user'), self::getConfiguration('password')))) {
			throw new FatalError('Cannot connect to database', array('driver' => self::getConfiguration('driver'), 'driverId' => self::getConfiguration('driverId'), 'dbq' => self::getConfiguration('dbq')));
		}
	}
}