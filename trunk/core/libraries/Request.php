<?php

class Request extends Application {
	protected $query = NULL;
	protected $controller = NULL;
	protected $action = NULL;
	protected $parameters = NULL;
	
	public function __construct($query) {
		$this->setQuery($query);
	}
	
	protected function getAlias($query) {
		foreach ($this->getConfiguration('aliasQueries') as $pattern => $replacement) {
			$alias = preg_replace(sprintf('/^%s$/i', $pattern), $replacement, $query, -1, $replaced);
			if ($replaced) return $alias;
		}
		return $query;
	}
	
	protected function resolveQuery() {
		$query = $this->getQuery() ? $this->getQuery() : $this->getConfiguration('defaultQuery');
		return $this->getAlias($query);
	}
	
	public function analyze() {
		$segments = explode('/', $this->resolveQuery());
		if (is_string($segments)) $segments = array($segments);
		
		if (isset($segments[0]) && $segments[0]) $this->setController($segments[0]);
		else throw new FatalError('No controller defined', $segments);
		
		if (isset($segments[1]) && $segments[1]) $this->setAction($segments[1]);
		else throw new FatalError('No action defined', $segments);
		
		$this->setParameters(array_slice($segments, 2));
	}
}