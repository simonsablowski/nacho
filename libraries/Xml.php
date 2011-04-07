<?php

class Xml extends Application {
	public static function cleanProperty($value) {
		$value = preg_replace('/&(.+);/', '', $value);
		$value = preg_replace('/<br([\s\/]*)>/', "\n", $value);
		$value = preg_replace('/<(?:[^"\']+?|.+?(?:"|\').*?(?:"|\')?.*?)*?>/', '$1', $value);
		$value = str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $value);
		return $value;
	}
	
	protected static function dumpProperty($property, $value, $indent = 1) {
		if (is_array($value)) {
			$dump = sprintf("%s<%s>\n", str_repeat("\t", $indent), $property);
			foreach ($value as $itemsProperty => $itemsValue) {
				$dump .= self::dumpProperty(is_string($itemsProperty) ? $itemsProperty : 'element', $itemsValue, $indent + 1);
			}
			$dump .= sprintf("%s</%s>\n", str_repeat("\t", $indent), $property);
			return $dump;
		} else if (is_string($value) || is_int($value)) {
			return sprintf("%s<%s>%s</%s>\n", str_repeat("\t", $indent), $property, self::cleanProperty($value), $property);
		}
		return NULL;
	}
	
	public static function dumpObject($Object, $name, $indent = 0) {
		if (is_object($Object)) {
			$properties = array();
			foreach (get_object_vars($Object) as $field => $value) {
				try {
					$Property = new ReflectionProperty($Object->getClassName(), $field);
					if (!$Property->isPublic()) continue;
				} catch (ReflectionException $Exception) {
					
				}
				$properties[$field] = $value;
			}
			return self::dumpProperty($name, $properties, $indent);
		}
		return NULL;
	}
}