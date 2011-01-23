<?php

abstract class Controller extends Application {
	protected $Session = NULL;
	protected $User = NULL;
	
	public function __construct() {
		
	}
	
	protected function performAction($actionName, $parameters) {
		$ReflectionMethod = new ReflectionMethod($this, $actionName);
		
		if (count($ReflectionMethod->getParameters()) <= count($parameters)) {
			call_user_func_array(array($this, $actionName), $parameters);
		} else {
			throw new FatalError('Missing parameters', array('Controller' => $this->getClassName(), 'Action' => $actionName, 'Parameters' => $parameters));
		}
	}
}