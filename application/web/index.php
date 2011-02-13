<?php

error_reporting(E_ALL);

require_once dirname(__FILE__) . '/../configuration.php';

function __autoload($className) {
	global $configuration;
	
	foreach ($configuration['includeDirectories'] as $includeDirectory) {
		if (!findClass($className, $includeDirectory)) continue;
	}
}

function findClass($className, $filePath) {
	if (($namePart = strstr($className, 'Controller', TRUE)) !== FALSE && file_exists($filePath . ($fileName = 'controllers/' . $namePart . 'Controller.php'))) {
		include_once $filePath . $fileName;
		return TRUE;
	} else if (file_exists($filePath . ($fileName = 'models/' . $className . '.php'))) {
		include_once $filePath . $fileName;
		return TRUE;
	} else if (file_exists($filePath . ($fileName = 'libraries/' . $className . '.php'))) {
		include_once $filePath . $fileName;
		return TRUE;
	} else if (file_exists($filePath . ($fileName = $className . '.php'))) {
		include_once $filePath . $fileName;
		return TRUE;
	}
	
	return FALSE;
}

$Application = new Application($configuration);
$Application->run(isset($_GET['query']) ? $_GET['query'] : NULL);