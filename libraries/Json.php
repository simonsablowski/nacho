<?php

class Json extends Application {
	public static function encode($value) {
		return json_encode($value);
	}
	
	public static function decode($value) {
		return json_decode($value);
	}
}