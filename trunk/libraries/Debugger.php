<?php

class Debugger extends Application {
	public function __construct() {
		
	}
	
	public function log($value) {
		$handle = fopen($this->getConfiguration('logPath'), 'a');
		$result = fwrite($handle, sprintf("[%s] %s\n", date('Y-m-d H:i:s', time()), $value));
		fclose($handle);
		return $result;
	}
}