<?php

abstract class Controller extends Application {
	public function __construct() {
		
	}
	
	protected function performAction($actionName, $parameters) {
		$Reflection = new ReflectionClass($this);
		if (!$Reflection->hasMethod($actionName)) {
			throw new FatalError('Invalid action', array('Controller' => $this->getClassName(), 'Action' => $actionName, 'Parameters' => $parameters));
		}
		
		if (count($Reflection->getMethod($actionName)->getParameters()) <= count($parameters)) {
			call_user_func_array(array($this, $actionName), $parameters);
		} else {
			throw new FatalError('Missing parameters', array('Controller' => $this->getClassName(), 'Action' => $actionName, 'Parameters' => $parameters));
		}
	}
}