<?php

class ErrorHandler extends Application {
	protected $OutputBuffer = NULL;
	protected $Session = NULL;
	
	public function __construct() {
		
	}
	
	public function handle($Error) {
		switch ($Error->getType()) {
			default:
			case 'Warning':
			case 'Fatal':
				$this->getSession()->setData('Error', $Error);
				break;
		}
	}
}