<?php

class ErrorHandler extends Application {
	public function __construct() {
		
	}
	
	public function handle(Error $Error) {
		switch ($Error->getType()) {
			default:
			case 'Warning':
				$this->getSession()->setData('Error', $Error);
				break;
			case 'Fatal':
				$this->getOutputBuffer()->clean();
				$this->displayView('Error.php', array(
					'Error' => $Error
				));
				break;
		}
	}
}