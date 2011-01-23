<?php

abstract class Controller extends Application {
	protected $Session = NULL;
	
	public function __construct() {
		
	}
	
	protected function performAction($actionName, $parameters) {
		call_user_func_array(array($this, $actionName), $parameters);
	}
}