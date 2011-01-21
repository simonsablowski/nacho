<?php

abstract class Controller {
	protected $User = NULL;
	
	public function __construct() {
		parent::__construct();
		
		header('Content-Type: text/xml; charset=utf-8');
		printf("<?xml version=\"1.0\" encoding=\"utf-8\"?>");
	}
	
	//TODO: move this to mother class
	protected function print($line, $arguments = array()) {
		vprintf("\n" . $line, is_array($arguments) ? $arguments : array($arguments));
	}
}
