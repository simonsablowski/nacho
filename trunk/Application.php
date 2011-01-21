<?php

class Application {
	protected $request = NULL;
	protected $controller = NULL;
	protected $action = NULL;
	
	public function __call($method, $parameters) {
		preg_match_all('/(^|[A-Z]{1})([a-z]+)/', $method, $methodParts);
		if (!isset($methodParts[0][0]) || !isset($methodParts[0][1])) throw new Exception('Invalid method format', $method);
		
		$operation = $methodParts[0][0];
		array_shift($methodParts[0]);
		
		$name = implode('', $methodParts[0]);
		$property = strtolower(substr($name, 0, 1)) . substr($name, 1);
		if (!property_exists($this, $property)) throw new Exception('Undeclared property', $property);
		
		switch ($operation) {
			case 'get':
				return self::$property;
			case 'is':
				return self::$property == 'yes';
			case 'set':
				self::$property = $parameters[0];
				return;
		}
	}
	
	public function __construct($request) {
		$this->setRequest = $request;
	}
	
	final public function run() {
		try {
			$this->setup();
			$this->perform();
		} catch (Exception $Exception) {
			//TODO
		}
	}
	
	final private function setup() {
		error_reporting(E_ALL);
		
		Session::start();
		Database::connect();
		Localization::initialize();
		
		$this->analyzeRequest();
		$this->initializeController();
	}
	
	final private function analyzeRequest() {
		//TODO
	}
	
	final private function initializeController() {
		//TODO
	}
	
	final private function perform() {
		$actionName = $this->getAction();
		$this->getController()->$actionName();
	}
	
	protected function print($line, $arguments = array()) {
		vprintf("\n" . $line, is_array($arguments) ? $arguments : array($arguments));
	}
}