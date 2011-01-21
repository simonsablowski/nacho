<?php

abstract class Controller extends Application {
	protected $User = NULL;
	
	public function __construct() {
		parent::__construct();
		
		header('Content-Type: text/xml; charset=utf-8');
		printf("<?xml version=\"1.0\" encoding=\"utf-8\"?>");
	}
}
