<?php

class Debugger extends Application {
	public function __construct() {
		
	}
	
	public function log($value) {
		if (is_null($logPath = $this->getConfiguration('logPath'))) {
			throw new FatalError('Log path not set');
		}
		
		$handle = fopen($logPath, 'a');
		$result = fwrite($handle, sprintf("[%s] %s\n", date('Y-m-d H:i:s', time()), var_export($value, TRUE)));
		fclose($handle);
		return $result;
	}
}