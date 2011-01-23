<?php

class Application {
	protected $configuration = array();
	private $ErrorHandler = NULL;
	private $Request = NULL;
	private $OutputBuffer = NULL;
	private $Session = NULL;
	private $Controller = NULL;
	
	protected static function resolveMethod($className, $method) {
		preg_match_all('/(^|[A-Z]{1})([a-z]*)/', $method, $methodParts);
		if (!isset($methodParts[0][0]) || !isset($methodParts[0][1])) throw new FatalError('Invalid method format', $method);
		
		$operation = $methodParts[0][0];
		array_shift($methodParts[0]);
		
		$propertyCapitalized = implode('', $methodParts[0]);
		$property = strtolower(substr($propertyCapitalized, 0, 1)) . substr($propertyCapitalized, 1);
		
		$propertyExists = FALSE;
		
		if (property_exists($className, $property)) {
			$propertyExists = TRUE;
		} else if (property_exists($className, $propertyCapitalized)) {
			$propertyExists = TRUE;
			$property = $propertyCapitalized;
		}
		
		return array($operation, $property, $propertyExists, $propertyCapitalized, $methodParts);
	}
	
	public function __call($method, $parameters) {
		list($operation, $property, $propertyExists) = $this->resolveMethod($this, $method);
		if (!$propertyExists) throw new FatalError('Undeclared property', $property);
		
		switch ($operation) {
			case 'get':
				return $this->$property;
			case 'is':
				return $this->$property === TRUE;
			case 'set':
				return $this->$property = $parameters[0];
		}
	}
	
	public static function __callStatic($method, $parameters) {
		$className = get_called_class();
		
		list($operation, $property, $propertyExists) = self::resolveMethod($className, $method);
		if (!$propertyExists) throw new FatalError('Undeclared property', $property);
		
		switch ($operation) {
			case 'get':
				return $className::$$property;
			case 'is':
				return $className::$$property === TRUE;
			case 'set':
				return $className::$$property = $parameters[0];
		}
	}
	
	public function __construct($configuration) {
		$this->setConfiguration($configuration);
	}
	
	final public function run($query) {
		try {
			$this->setOutputBuffer(new OutputBuffer);
			$this->getOutputBuffer()->start();
			$this->setup($query);
			$this->getController()->performAction($this->getRequest()->getAction(), $this->getRequest()->getParameters());
			$this->getOutputBuffer()->flush();
		} catch (Error $Error) {
			$this->getErrorHandler()->handle($Error);
			$this->getOutputBuffer()->flush();
		}
	}
	
	final private function setup($query) {
		if ($header = $this->getConfiguration('header')) {
			header($header);
		}
		
		$this->initializeErrorHandler();
		$this->initializeRequest($query);
		$this->initializeSession();
		$this->initializeDatabase();
		$this->initializeController();
	}
	
	final private function initializeRequest($query) {
		$this->setRequest(new Request($query));
		$this->getRequest()->setConfiguration($this->getConfiguration());
		$this->getRequest()->analyze();
	}
	
	final private function initializeErrorHandler() {
		$this->setErrorHandler(new ErrorHandler);
		$this->getErrorHandler()->setOutputBuffer($this->getOutputBuffer());
		$this->getErrorHandler()->setSession($this->getSession());
	}
	
	final private function initializeSession() {
		$this->setSession(new Session);
		$this->getSession()->start();
	}
	
	final private function initializeDatabase() {
		Database::initialize($this->getConfiguration('Database'));
		Database::connect();
	}
	
	final private function initializeController() {
		$ControllerName = $this->getRequest()->getController() . 'Controller';
		if (!class_exists($ControllerName)) throw new FatalError('Invalid controller', $ControllerName);
		
		$this->setController(new $ControllerName);
		$this->getController()->setConfiguration($this->getConfiguration());
		$this->getController()->setSession($this->getSession());
	}
	
	protected static function getClassName() {
		return get_called_class();
	}
	
	protected function getConfiguration($field = NULL) {
		return !is_null($field) ? (isset($this->configuration[$field]) ? $this->configuration[$field] : NULL) : $this->configuration;
	}
	
	protected function displayView($View, $variables = array()) {
		extract($variables);		
		include dirname(__FILE__) . '/Views/' . $View;
	}
}