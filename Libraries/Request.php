<?php

class Request extends Application {
	protected $query = NULL;
	protected $controller = NULL;
	protected $action = NULL;
	protected $parameters = NULL;
	
	public function __construct($query) {
		$this->setQuery($query);
	}
	
	public function analyze() {
		$segments = $this->getQuery() ? explode('/', $this->getQuery()) : array();
		
		$this->setController(isset($segments[0]) ? $segments[0] : $this->getConfiguration('defaultController'));
		$this->setAction(isset($segments[1]) ? $segments[1] : $this->getConfiguration('defaultAction'));
		$this->setParameters(array_slice($segments, 2));
	}
}