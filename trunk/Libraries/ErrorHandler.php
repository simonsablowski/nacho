<?php

class ErrorHandler extends Application {
	protected $OutputBuffer = NULL;
	protected $Session = NULL;
	
	public function __construct() {
		
	}
	
	public function handle($Error) {
		switch ($Error->getType()) {
			default:
			// case 'Warning':
				// $this->getSession()->setData('Error', $Error);
				// break;
			case 'Fatal':
				$this->getOutputBuffer()->clean();
				$this->displayView('Error.xml', array(
					'Error' => $Error
				));
				break;
		}
	}
}