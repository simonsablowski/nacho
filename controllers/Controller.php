<?php

abstract class Controller extends Application {
	public function __construct() {
		
	}
	
	protected function performAction($actionName, $parameters) {
		if (!$this->hasMethod($actionName) || !$this->getMethod($actionName)->isPublic()) {
			throw new FatalError('Invalid action', array('Controller' => $this->getClassName(), 'Action' => $actionName, 'Parameters' => $parameters));
		}
		
		if (count($this->getMethod($actionName)->getParameters()) > count($parameters)) {
			throw new FatalError('Missing parameters', array('Controller' => $this->getClassName(), 'Action' => $actionName, 'Parameters' => $parameters));
		}
		
		if ($this->hasMethod('autoload')) {
			spl_autoload_register(array($this, 'autoload'));
		}
		
		call_user_func_array(array($this, $actionName), $parameters);
		
		$this->getOutputBuffer()->flush();
	}
}
