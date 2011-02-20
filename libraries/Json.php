<?php

class Json extends Application {
	public static function encode($value) {
		return json_encode($value);
	}
	
	public static function decode($value) {
		return json_decode($value);
	}
	
	public static function convertToXml($value, $name, $indent = 0) {
		return Xml::dumpObject(self::decode($value), $name, $indent);
	}
}