<?php

class Session extends Application {
	public function __construct($sessionId = NULL) {
		if (!is_null($sessionId)) session_id($sessionId);
	}
	
	public function start() {
		return session_start();
	}
	
	public function getId() {
		return session_id();
	}
	
	public function getData($field = NULL) {
		if (is_null($field)) return $_SESSION;
		else if (isset($_SESSION[$field])) return $_SESSION[$field];
		else return NULL;
	}
	
	public function setData() {
		if (func_num_args() == 2) {
			return $_SESSION[func_get_arg(0)] = func_get_arg(1);
		} else if (func_num_args() == 1) {
			$data = func_get_arg(0);
			return $_SESSION = array_merge($_SESSION, $data);
		}
	}
	
	public function destroy() {
		return session_destroy();
	}
}