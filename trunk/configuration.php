<?php

$configuration = array(
	'Database' => array(
		'type' => 'MySql',
		'host' => 'localhost',
		'name' => 'nacho',
		'user' => 'root',
		'password' => ''
	),
	'defaultQuery' => 'index',
	'aliasQueries' => array(
		'index' => 'Home/index'
	)
);