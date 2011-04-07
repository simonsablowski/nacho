<?php

class OutputBuffer extends Application {
	public function __construct() {
		
	}
	
	public function start() {
		return ob_start();
	}
	
	public function read() {
		return ob_get_contents();
	}
	
	public function clean() {
		return ob_clean();
	}
	
	public function flush() {
		return ob_flush();
	}
	
	public function get() {
		return ob_get_clean();
	}
}