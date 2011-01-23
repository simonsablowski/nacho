<?php

$configuration = array(
	'baseDirectory' => '/nacho/public/',
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
	)
);