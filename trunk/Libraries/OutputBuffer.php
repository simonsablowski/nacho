<?php

class OutputBuffer extends Application {
	public function __construct() {
		
	}
	
	public function start() {
		ob_start();
	}
	
	public function clean() {
		ob_clean();
	}
	
	public function flush() {
		ob_flush();
	}
}