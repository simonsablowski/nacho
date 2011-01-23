<?php

abstract class Controller extends Application {
	protected $Session = NULL;
	protected $User = NULL;
	
	public function __construct() {
		
	}
	
	protected function updateUser() {
		if ($User = $this->getSession()->getData('User')) {
			return $this->setUser($User);
		} else {
			return $this->setTemporaryUser();
		}
	}
	
	protected function setTemporaryUser() {
		return $this->setUser(new TemporaryUser);
	}
	
	protected function isSignedIn() {
		return !$this->getUser()->isTemporary();
	}
	
	protected function performAction($actionName, $parameters) {
		call_user_func_array(array($this, $actionName), $parameters);
	}
}