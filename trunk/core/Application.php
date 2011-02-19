<?php

class Application {
	private $path = NULL;
	protected $configuration = array();
	protected $OutputBuffer = NULL;
	protected $Reflection = NULL;
	protected $Session = NULL;
	private $ErrorHandler = NULL;
	protected $Localization = NULL;
	protected $Request = NULL;
	private $Controller = NULL;
	protected $Application = NULL;
	protected $variables = array();
	
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
		$this->setPath(($path = $this->getConfiguration('pathApplication')) ? $path : dirname(__FILE__) . '/../application/');
	}
	
	private function getInstance($className, $parameters = NULL) {
		$Instance = new $className($parameters);
		$Instance->setApplication($this);
		$Instance->initializeReflection();
		
		return $Instance;
	}
	
	public function run($query) {
		try {
			$this->initializeOutputBuffer();
			$this->setup($query);
			$this->getController()->performAction($this->getRequest()->getAction(), $this->getRequest()->getParameters());
			$this->getOutputBuffer()->flush();
		} catch (Error $Error) {
			$this->getErrorHandler()->handle($Error);
			$this->getOutputBuffer()->flush();
		}
	}
	
	private function initializeOutputBuffer() {
		$this->setOutputBuffer($this->getInstance('OutputBuffer'));
		$this->getOutputBuffer()->start();
	}
	
	private function setup($query) {
		if ($header = $this->getConfiguration('header')) header($header);
		
		$this->initializeReflection();
		$this->initializeSession();
		$this->initializeErrorHandler();
		$this->initializeLocalization();
		$this->initializeRequest($query);
		$this->initializeDatabase();
		$this->initializeController();
	}
	
	private function initializeReflection() {
		$this->setReflection(new ReflectionClass($this));
	}
	
	private function initializeSession() {
		$this->setSession($this->getInstance('Session'));
		$this->getSession()->start();
	}
	
	private function initializeErrorHandler() {
		$this->setErrorHandler($this->getInstance('ErrorHandler'));
		$this->getErrorHandler()->setOutputBuffer($this->getOutputBuffer());
		$this->getErrorHandler()->setSession($this->getSession());
	}
	
	private function initializeLocalization() {
		if (!$configuration = $this->getConfiguration('Localization')) return;
		
		$this->setLocalization($this->getInstance('Localization'));
		$this->getLocalization()->setConfiguration($this->getConfiguration('Localization'));
		$this->getLocalization()->prepare();
		$this->getErrorHandler()->setLocalization($this->getLocalization());
	}
	
	private function initializeRequest($query) {
		$this->setRequest($this->getInstance('Request', $query));
		$this->getRequest()->setConfiguration($this->getConfiguration());
		$this->getRequest()->analyze();
	}
	
	private function initializeDatabase() {
		if (!$configuration = $this->getConfiguration('Database')) return;
		
		Database::initialize($configuration);
		Database::connect();
	}
	
	private function initializeController() {
		$name = $this->getRequest()->getController() . 'Controller';
		if (!class_exists($name)) throw new FatalError('Invalid controller', $name);
		
		$this->setController($this->getInstance($name));
		$this->getController()->setConfiguration($this->getConfiguration());
		$this->getController()->setSession($this->getSession());
		$this->getController()->setLocalization($this->getLocalization());
		$this->getController()->setRequest($this->getRequest());
	}
	
	protected static function getClassName() {
		return get_called_class();
	}
	
	protected function hasMethod($methodName) {
		return $this->getReflection()->hasMethod($methodName);
	}
	
	protected function getMethod($methodName) {
		return $this->getReflection()->getMethod($methodName);
	}
	
	protected function localize($string, $replacements = NULL) {
		if (!is_object($this->getLocalization())) return Localization::getReplaced($string, $replacements);
		
		return $this->getLocalization()->getLocalized($string, $replacements);
	}
	
	protected function getConfiguration($field = NULL) {
		return !is_null($field) ? (isset($this->configuration[$field]) ? $this->configuration[$field] : NULL) : $this->configuration;
	}
	
	protected function setVariables($variables) {
		$this->variables = array_merge($this->variables, $variables);
	}
	
	protected function displayView($view, $variables = array()) {
		$this->setVariables($variables);		
		extract($this->getVariables());		
		include $this->getApplication()->getPath() . 'views/' . $view;
	}
}