<?php

$configuration = array(
	'baseDirectory' => '/nacho/',
	'Database' => array(
		'type' => 'MySql',
		'host' => 'localhost',
		'name' => 'nacho',
		'user' => 'root',
		'password' => ''
	),
	'defaultQuery' => 'index',
	'aliasQueries' => array(
		'index' => 'Food/index'
	),
	// 'debugMode' => TRUE
	'debugMode' => FALSE
);