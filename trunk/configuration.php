<?php

$configuration = array(
	'header' => 'Content-Type: text/html; charset=utf-8',
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