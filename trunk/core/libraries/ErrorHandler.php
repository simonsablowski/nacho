<?php

class ErrorHandler extends Application {
	public function __construct() {
		
	}
	
	public function handle(Error $Error) {
		$this->getOutputBuffer()->clean();
		$this->displayView('Error.show.php', array(
			'Error' => $Error
		));
	}
}