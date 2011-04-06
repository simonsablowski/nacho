<?php

class ErrorHandler extends Application {
	public function __construct() {
		set_error_handler(array($this, 'handleError'));
		set_exception_handler(array($this, 'handleException'));
	}
	
	public function handleError($number, $message, $file = NULL, $line = NULL, $context = NULL) {
		throw new FatalError($message, array(
			'number' => $number,
			'file' => $file,
			'line' => $line,
			'context' => $context
		));
	}
	
	public function handleException(Exception $Exception) {
		$this->getOutputBuffer()->clean();
		$this->displayView('Error.show.php', array(
			'Error' => $Exception
		));
		$this->getOutputBuffer()->flush();
	}
}