<?php

class ErrorHandler extends Application {
	public function __construct() {
		set_error_handler(array($this, 'handleError'));
	}
	
	public function handleError($number, $message, $file = NULL, $line = NULL, $context = NULL) {
		throw new FatalError($message, array(
			'number' => $number,
			'file' => $file,
			'line' => $line,
			'context' => $context
		));
	}
	
	public function handle(Error $Error) {
		$this->getOutputBuffer()->clean();
		$this->displayView('Error.show.php', array(
			'Error' => $Error
		));
	}
}