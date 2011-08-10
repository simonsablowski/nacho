<?php

class Application {
	protected $path = NULL;
	protected $configuration = array();
	protected $OutputBuffer = NULL;
	protected $Reflection = NULL;
	protected $Debugger = NULL;
	protected $Session = NULL;
	protected $ErrorHandler = NULL;
	protected $Localization = NULL;
	protected $Request = NULL;
	protected $Controller = NULL;
	protected $Application = NULL;
	protected $variables = array();
	
	protected static function resolveMethod($className, $method) {
		preg_match_all('/(^|[A-Z]{1})([a-z0-9]*)/', $method, $methodParts);
		if (!isset($methodParts[0]) || !$methodParts[0]) throw new FatalError('Invalid method format', $method);
		
		$operation = array_shift($methodParts[0]);
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
		$className = self::getClassName();
		
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
	
	protected static function stripOutNamespace(&$className) {
		$className = ($part = strrchr($className, '\\')) ? substr($part, 1) : $className;
	}
	
	public function __construct($configuration, $localization = NULL) {
		$this->setConfiguration($configuration);
		$this->setPath(($path = $this->getConfiguration('pathApplication')) ? $path : dirname(__FILE__) . '/../application/');
		
		$this->registerAutoload();
		$this->setExecutionTime();
		
		if (isset($configuration['Localization'])) {
			if (is_null($localization) && isset($configuration['Localization']['default'])) {
				$localization = $configuration['Localization']['default'];
			}
			if (isset($configuration['Localization'][$localization])) {
				$configurationLocalization = $configuration['Localization'][$localization];
			} else {
				$configurationLocalization = array(
					'language' => $localization,
					'locale' => $localization
				);
			}
			$this->setConfiguration(array_merge($configuration, array(
				'Localization' => $configurationLocalization
			)));
		}
	}
	
	protected function registerAutoload() {
		spl_autoload_register(array($this, 'autoload'));
	}
	
	protected function autoload($className) {
		if ($classPaths = $this->getConfiguration('classPaths') && isset($classPaths[$className])) {
			return include_once($classPaths[$className]);
		}
		
		foreach ($this->getConfiguration('includeDirectories') as $includeDirectory) {
			if ($this->findClass($className, $includeDirectory)) return TRUE;
		}
		
		return FALSE;
	}
	
	protected function findClass($className, $filePath) {
		$this->stripOutNamespace($className);
		
		if (($namePart = strstr($className, 'Controller', TRUE)) !== FALSE && file_exists($filePath . ($fileName = 'controllers/' . $namePart . 'Controller.php'))) {
			return include_once $filePath . $fileName;
		} else if (file_exists($filePath . ($fileName = 'models/' . $className . '.php'))) {
			return include_once $filePath . $fileName;
		} else if (file_exists($filePath . ($fileName = 'libraries/' . $className . '.php'))) {
			return include_once $filePath . $fileName;
		} else if (file_exists($filePath . ($fileName = $className . '.php'))) {
			return include_once $filePath . $fileName;
		}
		
		return FALSE;
	}
	
	protected function setExecutionTime() {
		if ($executionTime = $this->getConfiguration('executionTime')) {
			ini_set('max_execution_time', $executionTime);
		}
	}
	
	protected function getInstance($className, $parameters = NULL) {
		$Instance = new $className($parameters);
		$Instance->setApplication($this);
		$Instance->setupReflection();
		
		return $Instance;
	}
	
	public function run($query) {
		$this->setup($query);
		$this->getController()->performAction($this->getRequest()->getAction(), $this->getRequest()->getParameters());
	}
	
	protected function setup($query) {
		$this->setupOutputBuffer();
		$this->setupHeader();
		$this->setupReflection();
		$this->setupSession();
		$this->setupErrorHandler();
		$this->setupDebugger();
		$this->setupLocalization();
		$this->setupRequest($query);
		$this->setupDatabase();
		$this->setupController();
	}
	
	protected function setupOutputBuffer() {
		$this->setOutputBuffer($this->getInstance('OutputBuffer'));
		$this->getOutputBuffer()->start();
	}
	
	protected function setupHeader() {
		if ($header = $this->getConfiguration('header')) header($header);
	}
	
	protected function setupReflection() {
		$this->setReflection(new ReflectionClass($this));
	}
	
	protected function setupSession() {
		if ($this->getConfiguration('ignoreSession')) return;
		
		$this->setSession($this->getInstance('Session'));
		$this->getSession()->start();
	}
	
	protected function setupErrorHandler() {
		if ($this->getConfiguration('disableErrorHandler')) return;
		
		$this->setErrorHandler($this->getInstance('ErrorHandler'));
		$this->getErrorHandler()->setOutputBuffer($this->getOutputBuffer());
		$this->getErrorHandler()->setSession($this->getSession());
	}
	
	protected function setupDebugger() {
		if (!$configuration = $this->getConfiguration('Debugger')) return;
		
		$this->setDebugger($this->getInstance('Debugger'));
		$this->getDebugger()->setConfiguration($configuration);
	}
	
	protected function setupLocalization() {
		if (!$configuration = $this->getConfiguration('Localization')) return;
		
		$this->setLocalization($this->getInstance('Localization'));
		$this->getLocalization()->setConfiguration($configuration);
		$this->getLocalization()->prepare();
		$this->getErrorHandler()->setLocalization($this->getLocalization());
	}
	
	protected function setupRequest($query) {
		$this->setRequest($this->getInstance('Request', $query));
		$this->getRequest()->setConfiguration(($configuration = $this->getConfiguration('Request')) ? $configuration : array());
		$this->getRequest()->analyze();
	}
	
	protected function setupDatabase() {
		if (!$configuration = $this->getConfiguration('Database')) return;
		
		Database::setup($configuration);
		Database::connect();
	}
	
	protected function setupController() {
		$name = $this->getRequest()->getController() . 'Controller';
		if (!class_exists($name)) throw new FatalError('Invalid controller', $name);
		
		$this->setController($this->getInstance($name));
		$this->getController()->setConfiguration($this->getConfiguration());
		$this->getController()->setOutputBuffer($this->getOutputBuffer());
		$this->getController()->setSession($this->getSession());
		$this->getController()->setDebugger($this->getDebugger());
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
	
	protected function setConfiguration() {
		if (func_num_args() == 2) {
			return $this->configuration[func_get_arg(0)] = func_get_arg(1);
		} else if (func_num_args() == 1) {
			$configuration = func_get_arg(0);
			return $this->configuration = array_merge($this->configuration, $configuration);
		}
	}
	
	protected function getConfiguration($field = NULL) {
		return !is_null($field) ? (isset($this->configuration[$field]) ? $this->configuration[$field] : NULL) : $this->configuration;
	}
	
	protected function setVariables($variables) {
		return $this->variables = array_merge($this->variables, $variables);
	}
	
	protected function isView($view) {
		$viewsDirectory = ($viewsDirectory = $this->getApplication()->getConfiguration('viewsDirectory')) ? $viewsDirectory : 'views/';
		
		foreach ($this->getApplication()->getConfiguration('includeDirectories') as $includeDirectory) {
			if (file_exists($file = $includeDirectory . $viewsDirectory . $view)) return $file;
		}
		
		return FALSE;
	}
	
	protected function displayView($view, $variables = array()) {
		if (!$file = $this->isView($view)) {
			throw new FatalError('View not found', $view);
		}
		
		$this->setVariables($variables);		
		extract($this->getVariables());		
		include $file;
	}
}