<?php

error_reporting(E_ALL);

$pathCore = dirname(__FILE__) . '/../../core/';
$pathApplication = dirname(__FILE__) . '/../';

require_once $pathCore . 'Application.php';
require_once $pathApplication . 'configuration.php';

function __autoload($className) {
	global $pathCore, $pathApplication;
	
	if (!findClass($className, $pathApplication)) {
		findClass($className, $pathCore);
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
	}
	return FALSE;
}

$Application = new Application($configuration);
$Application->run(isset($_GET['query']) ? $_GET['query'] : NULL);