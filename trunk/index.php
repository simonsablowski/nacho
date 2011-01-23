<?php

error_reporting(E_ALL);

require_once 'configuration.php';
require_once 'Application.php';

function __autoload($className) {
	$filePath = dirname(__FILE__) . '/';
	
	if (($namePart = strstr($className, 'Controller', TRUE)) !== FALSE && file_exists($filePath . ($fileName = 'Controllers/' . $namePart . 'Controller.php'))) {
		include_once $filePath . $fileName;
	} else if (file_exists($filePath . ($fileName = 'Models/' . $className . '.php'))) {
		include_once $fileName;
	} else if (file_exists($filePath . ($fileName = 'Libraries/' . $className . '.php'))) {
		include_once $fileName;
	}
}

$Application = new Application($configuration);
$Application->run(isset($_GET['query']) ? $_GET['query'] : NULL);