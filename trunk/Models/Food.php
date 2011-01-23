<?php

abstract class Food extends Model {
	protected static $name = NULL;
	protected static $nutritionFacts = NULL;
	protected static $price = NULL;
	
	final public static function getName($className = FALSE) {
		return !is_null($name = parent::getName()) && !$className ? $name : get_called_class();
	}
}