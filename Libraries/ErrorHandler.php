<?php

class ErrorHandler extends Application {
	public function __construct() {
		
	}
	
	public function handle($Error) {
		switch ($Error->getType()) {
			default:
			case 'Warning':
			case 'Fatal':
				$this->getSession()->setData('Error', $Error);
				$this->displayView('Error.php', array(
					'Error' => $Error
				));
				break;
		}
	}
}