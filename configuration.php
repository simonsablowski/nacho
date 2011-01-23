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
	'Request' => array(
		'defaultController' => 'Coaching',
		'defaultAction' => 'index'
	)
);