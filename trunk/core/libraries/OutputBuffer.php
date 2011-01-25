<?php

class OutputBuffer extends Application {
	public function __construct() {
		
	}
	
	public function start() {
		return ob_start();
	}
	
	public function clean() {
		return ob_clean();
	}
	
	public function flush() {
		return ob_flush();
	}
}