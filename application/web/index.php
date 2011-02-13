<?php

error_reporting(E_ALL);

require_once dirname(__FILE__) . '/../configuration.php';

function __autoload($className) {
	global $configuration;
	
	foreach ($configuration['includeDirectories'] as $includeDirectory) {
		if (findClass($className, $includeDirectory)) return;
	}
}

function findClass($className, $filePath) {
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

$Application = new Application($configuration);
$Application->run(isset($_GET['query']) ? $_GET['query'] : NULL);