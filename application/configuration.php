<?php

$configuration = array();

$configuration['pathApplication'] = dirname(__FILE__) . '/';

$configuration['basePath'] = '/nacho/application/web/';

$configuration['includeDirectories'] = array(
	$configuration['pathApplication'],
	$configuration['pathApplication'] . '../core/'
);

$configuration['Request'] = array(
	'defaultQuery' => 'Food/index',
	'aliasQueries' => array()
);

$configuration['debugMode'] = TRUE;
// $configuration['debugMode'] = FALSE;